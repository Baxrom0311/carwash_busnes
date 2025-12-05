<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymeTransaction;
use App\Models\TenantInvoice;
use Illuminate\Http\Request;

class PaymeController extends Controller
{
    public function handle(Request $request)
    {
        // 1. Avtorizatsiyani tekshirish (hozircha simulyatsiya)
        $authorization = $request->header('Authorization');
        // Haqiqiy loyihada bu yerda Payme yuborgan login va parol (base64)
        // bizning .env faylidagi kalitlar bilan solishtiriladi.
        // Hozircha bu qadamni o'tkazib turamiz.

        // 2. Kelgan so'rovni tahlil qilish
        $method = $request->input('method');
        $params = $request->input('params');

        // 3. Qaysi metod chaqirilganiga qarab, tegishli funksiyani ishga tushiramiz
        switch ($method) {
            case 'CheckPerformTransaction':
                return $this->checkPerformTransaction($params);
            case 'CreateTransaction':
                return $this->createTransaction($params);
            case 'PerformTransaction':
                return $this->performTransaction($params);
            // Bizga 'CancelTransaction', 'CheckTransaction' kabi boshqa metodlar ham kerak bo'ladi,
            // lekin hozircha shu uchtasi bilan cheklanamiz.
            default:
                return $this->errorResponse(-32601, 'Method not found');
        }
    }

    private function checkPerformTransaction($params)
    {
        // Hisob-fakturani topamiz
        $invoice = TenantInvoice::find($params['account']['invoice_id']);

        // 1. Hisob-faktura mavjudmi?
        if (!$invoice) {
            return $this->errorResponse(-31050, 'Invoice not found');
        }

        // 2. Hisob-faktura statusi 'pending'mi? (to'lanmaganmi?)
        if ($invoice->status !== 'pending') {
            return $this->errorResponse(-31050, 'Invoice is already paid or canceled');
        }

        // 3. Summa to'g'rimi? (Payme summani tiyinda yuboradi)
        if (($invoice->amount * 100) != $params['amount']) {
            return $this->errorResponse(-31001, 'Invalid amount');
        }

        // Hammasi to'g'ri bo'lsa, "to'lov qilish mumkin" degan javobni qaytaramiz
        return response()->json([
            'result' => [
                'allow' => true,
            ],
        ]);
    }

    private function createTransaction($params)
    {
        // Avval bu tranzaksiya ID'si bilan yozuv mavjudligini tekshiramiz
        $transaction = PaymeTransaction::where('paycom_id', $params['id'])->first();
        if ($transaction) {
            // Agar mavjud bo'lsa va vaqti to'g'ri kelsa, o'zini qaytaramiz
            if ($transaction->state == 1) {
                return $this->successCreateResponse($transaction);
            }
            return $this->errorResponse(-31050, 'Transaction error');
        }

        // CheckPerformTransaction'dagi tekshiruvlarni qayta bajaramiz
        $check = $this->checkPerformTransaction($params);
        if ($check->getData()->result->allow !== true) {
            return $check;
        }

        // Yangi tranzaksiya yozuvini yaratamiz
        $newTransaction = PaymeTransaction::create([
            'paycom_id' => $params['id'],
            'tenant_id' => TenantInvoice::find($params['account']['invoice_id'])->tenant_id,
            'tenant_invoice_id' => $params['account']['invoice_id'],
            'amount' => $params['amount'],
            'state' => 1, // state=1 -> created, but not performed
            'paycom_time' => $params['time'],
        ]);

        return $this->successCreateResponse($newTransaction);
    }

    private function performTransaction($params)
    {
        $transaction = PaymeTransaction::where('paycom_id', $params['id'])->first();

        // Tranzaksiya topilmadimi?
        if (!$transaction) {
            return $this->errorResponse(-31050, 'Transaction not found');
        }

        // Tranzaksiya statusi 'created' (1) holatidami?
        if ($transaction->state != 1) {
            // Agar allaqachon 'performed' (2) bo'lsa, xato emas, o'zini qaytaramiz
            if ($transaction->state == 2) {
                return $this->successPerformResponse($transaction);
            }
            return $this->errorResponse(-31050, 'Cannot perform this transaction');
        }

        // Tranzaksiya vaqtini yangilaymiz
        $transaction->state = 2; // performed
        $transaction->perform_time = now()->timestamp * 1000;
        $transaction->save();

        // *** ENG MUHIM QISM: XIZMATNI KO'RSATISH ***
        $invoice = $transaction->invoice;
        $invoice->status = 'paid';
        $invoice->paid_at = now();
        $invoice->save();

        // Tenantning obunasini uzaytiramiz
        $tenant = $invoice->tenant;
        $tenant->subscription_status = 'active';
        $tenant->next_billing_date = ($tenant->next_billing_date ?? now())->addMonth();
        $tenant->save();
        // O'ZGARISH: 'save()' o'rniga 'update()' ishlatamiz
        return $this->successPerformResponse($transaction);
    }

    // Yordamchi funksiyalar
    private function errorResponse($code, $message)
    {
        return response()->json([
            'error' => [
                'code' => $code,
                'message' => $message,
            ],
        ], 400);
    }

    private function successCreateResponse($transaction)
    {
        return response()->json([
            'result' => [
                'create_time' => $transaction->paycom_time,
                'transaction' => (string) $transaction->id,
                'state' => $transaction->state,
            ],
        ]);
    }

    private function successPerformResponse($transaction)
    {
        return response()->json([
            'result' => [
                'perform_time' => (int) $transaction->perform_time,
                'transaction' => (string) $transaction->id,
                'state' => $transaction->state,
            ],
        ]);
    }
}
