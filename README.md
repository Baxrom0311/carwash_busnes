# Car Wash Admin Panel (SaaS, Multi-tenant)

## Loyiha Tavsifi

Bu "Car Wash Admin Panel" - mashina yuvish shoxobchalari (moykalar) uchun mo'ljallangan, ko'p mijozli (Multi-tenant) SaaS (Software as a Service) ilovasi. U har bir moyka uchun avtomatlashtirilgan boshqaruv tizimini taqdim etadi, jumladan buyurtmalar, xizmatlar, avtomobillar, xodimlar, smenalar, to'lovlar va ish haqi hisob-kitoblarini boshqarish imkoniyatini beradi. Ilova zamonaviy web texnologiyalari (Vue.js va Laravel) asosida qurilgan.

**Biznes muammosi:** Viloyatdagi moykalarda mashina raqami, servis turlari, narxi va ishchining ismi qog'ozda yoziladi. Mashina tayyor bo'lgach, ish boshqaruvchidan kalit olinadi va yuvish sifatini tekshirib, ishchiga pul beriladi. Bu tizimni avtomatlashtirish orqali operatsiyalarni optimallashtirish, xatolarni kamaytirish va samaradorlikni oshirish asosiy maqsad hisoblanadi.

**Biznes modeli:** Moykalar tizimni har oyda ma'lum bir to'lov evaziga sotib oladilar (obuna asosida).

## Xususiyatlar

### Umumiy
*   **Multi-tenancy:** Har bir moyka (tenant) alohida ma'lumotlar bilan izolyatsiya qilingan.
*   **OTP (One-Time Password) Autentifikatsiya:** Telefon raqami orqali bir martalik parol yordamida xavfsiz kirish.
*   **API Documentation:** Backend API Swagger/OpenAPI yordamida to'liq hujjatlashtirilgan.
*   **Global Notifications:** Vuetify Snackbar orqali izchil xato va muvaffaqiyat xabarlari.

### Modullar
*   **Dashboard:** Umumiy statistika va asosiy ko'rsatkichlar.
*   **Buyurtmalar (Orders):**
    *   Yangi buyurtmalar yaratish, ularga bir nechta xizmatlarni dinamik qo'shish.
    *   Buyurtma tafsilotlarini ko'rish.
    *   Buyurtmalarni tahrirlash (status, izoh).
    *   Buyurtmalarni o'chirish.
*   **Xizmatlar (Services):** Xizmat turlarini (yuvish, tozalash va h.k.) va ularning narxlarini boshqarish (CRUD).
*   **Avtomobillar (Vehicles):** Moykaga kiradigan avtomobillar ro'yxatini boshqarish (davlat raqami, marka, model, rang, egasi) (CRUD).
*   **Xodimlar (Users):** Xodimlarni boshqarish, ularga rol berish (owner, manager, cashier, worker) (CRUD).
*   **To'lovlar (Payments):** Barcha amalga oshirilgan to'lovlar tarixini ko'rish (Read-only).
*   **Smenalar (Shifts):** Kassirlarning smena ochish/yopish va smenalar tarixini ko'rish (Read-only).
*   **Ish haqi qoidalari (Wage Rules):** Ishchilarning ish haqi qoidalarini belgilash (fixed/percent) (Backendda mavjud).
*   **Ish haqi yozuvlari (Wage Entries):** Har bir buyurtma itemi uchun ish haqi hisob-kitoblari (Backendda mavjud).
*   **Telegram Notifications:** OTP kodlari va boshqa bildirishnomalarni Telegram orqali yuborish.
*   **Payme Integration:** To'lov tizimi integratsiyasi (Backendda mavjud).

## Texnologiyalar

### Frontend
*   **Vue.js 3:** JavaScript freymvorki.
*   **Vuetify 3:** Material Design asosidagi UI komponentalar kutubxonasi.
*   **Pinia:** Vue.js ilovalari uchun holatni boshqarish kutubxonasi.
*   **Vue Router 4:** Sahifalar o'rtasida navigatsiya.
*   **Axios:** HTTP so'rovlari uchun mijoz.
*   **Vite:** Tezkor frontend build tool.

### Backend
*   **Laravel 11/12:** PHP freymvorki.
*   **PHP 8.2+**
*   **Laravel Sanctum:** SPA va mobil ilovalar uchun API autentifikatsiyasi.
*   **Spatie Packages:**
    *   `laravel-permission`: Foydalanuvchi rollari va ruxsatlarini boshqarish.
    *   `laravel-query-builder`: API so'rovlarida filtrlash, tartiblash uchun.
*   **darkaonline/l5-swagger:** Swagger/OpenAPI hujjatlarini avtomatik generatsiya qilish.
*   **lorisleiva/laravel-actions:** Domen mantiqini Action sinflari orqali inkapsulyatsiya qilish.
*   **laravel-notification-channels/telegram:** Telegram orqali bildirishnomalar.
*   **Database:** MySQL / PostgreSQL (yoki SQLite development uchun).

## Loyihani ishga tushirish

### Talablar (Prerequisites)
*   PHP >= 8.2
*   Composer
*   Node.js & npm / Yarn (Frontend uchun)
*   Ma'lumotlar bazasi (MySQL, PostgreSQL yoki SQLite)

### O'rnatish
1.  **Loyihani klonlash:**
    ```bash
    git clone https://github.com/sizning_username/carwash_project.git
    cd carwash_project
    ```
2.  **Backend o'rnatish:**
    ```bash
    cd carwash-backend
    composer install
    cp .env.example .env
    php artisan key:generate
    php artisan migrate --seed # Ma'lumotlar bazasini yaratish va dastlabki ma'lumotlarni to'ldirish
    # Test ma'lumotlari uchun: php artisan db:seed --class=TestDataSeeder
    ```
    `.env` faylini oching va ma'lumotlar bazasi konfiguratsiyasini sozlang. `APP_URL` ni `http://127.0.0.1:8000` qilib o'rnating.

3.  **Frontend o'rnatish:**
    ```bash
    cd ../carwash-frontend
    npm install # yoki yarn
    cp .env.example .env
    ```
    `.env` faylini oching va `VITE_API_URL` ni `http://127.0.0.1:8000/api` qilib o'rnating.

### Loyihani ishga tushirish
Har ikkala frontend va backend serverlarini bir vaqtda ishga tushirish uchun:

1.  **`carwash-backend`** katalogiga o'ting:
    ```bash
    cd carwash-backend
    ```
2.  **`composer run dev`** buyrug'ini ishga tushiring:
    ```bash
    composer run dev
    ```
    Bu buyruq Laravel development serverini (`php artisan serve`), navbat tinglovchisini (`php artisan queue:listen`), loglarni (`php artisan pail`) va frontend Vite serverini (`npm run dev`) bir vaqtda ishga tushiradi.

3.  Brauzeringizda **`http://localhost:5173`** (yoki `npm run dev` tomonidan berilgan boshqa port) manziliga o'ting.

## API Hujjatlari (Swagger)
Backend API hujjatlarini ko'rish uchun loyihani ishga tushirganingizdan so'ng brauzerda quyidagi manzilga o'ting:

**`http://127.0.0.1:8000/api/documentation`**

## Loyiha holati va Keyingi qadamlar

Loyiha **To'liq Funksional MVP (Minimal Hayotiy Mahsulot)** holatida. Barcha asosiy biznes talablar amalga oshirilgan va tizim to'liq ishga tushirishga tayyor.

### Bajarilgan ishlar:
*   Backend: OTP xeshlash, barcha CRUD va maxsus API nuqtalar, to'liq Authorization siyosatlari, murakkab domen mantiqi (Actions, QueryBuilder, Telegram Notif).
*   Frontend: Autentifikatsiya (Login/Logout), Global Snackbar, Barcha asosiy View'lar (Dashboard, Users, Services, Vehicles, Orders, Payments, Order Detail), barcha modullar uchun CRUD funksiyalari to'liq yakunlandi (Orders ham).
*   **Qolgan barcha API hujjatlari (Swagger DocBlocks) joyiga qo'yildi.**

### Keyingi takomillashtirishlar (ixtiyoriy):
1.  **Frontend Validatsiyasi:** Forma komponentalariga (masalan, `OrderFormModal.vue`) kuchliroq mijoz tomonidagi validatsiyani (VeeValidate/Vuelidate) integratsiya qilish.
2.  **Yuklanish Indikatorlari:** `handleSave` kabi amallar bajarilganda tugmalarda yoki modal ichida maxsus yuklanish indikatorlarini ko'rsatish.
3.  **API URL Manzilini `.env` dan olish:** `carwash-backend/config/app.php` yoki `config/services.php` da API `baseURL` ni `.env` faylidan olishga sozlash (hozirda `axios.js` da qattiq kodlangan).
4.  **Unit/Feature Testlarni yozish:** Loyihaning barqarorligini ta'minlash uchun test qamrovini oshirish.
5.  **Foydalanuvchi Profilini tahrirlash:** Frontendda foydalanuvchining o'z profilini (ism, parol, avatar) tahrirlash imkoniyatini qo'shish.
6.  **Ilova sozlamalari:** Moyka sozlamalari (masalan, obuna muddati, to'lov rejasi) uchun admin paneli funksionalligi.

## Muallif
[Ismingiz/GitHub Username]

## Litsenziya
Ushbu loyiha MIT litsenziyasi ostida. Batafsil ma'lumot uchun `LICENSE` fayliga qarang.