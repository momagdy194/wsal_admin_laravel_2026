# مرجع مسارات الـ API – المشروع بالكامل

القاعدة: `http(s)://your-domain/api` أو `http(s)://your-domain/api/v1`

- تم تسجيل كل المسارات عبر `RouteServiceProvider::mapApiRoutes()` الذي يحمّل `routes/api.php`.
- ملف `routes/api.php` يحمّل كل الملفات داخل `routes/api/v1/` عبر `include_route_files('api/v1')`.
- في `RouteServiceProvider::map()` يتم تحميل **مسارات الـ API أولاً** ثم الـ web حتى لا يلتقط الـ catch-all في الـ web طلبات `/api/*`.

---

## 1. مسارات من `routes/api.php` (بدون بادئة v1)

| Method | URI | Controller / الوصف |
|--------|-----|---------------------|
| GET | `/api/privacy-content` | LandingQuickLinkController@getPrivacyContent |
| GET | `/api/terms-content` | LandingQuickLinkController@getTermsContent |
| GET | `/api/compliance-content` | LandingQuickLinkController@getComplianceContent |
| GET | `/api/dmv-content` | LandingQuickLinkController@getDmvContent |

---

## 2. مسارات `api/v1` من `routes/api/v1/common.php`

### عام (بدون auth)

| Method | URI | Controller |
|--------|-----|------------|
| GET | `/api/v1/countries` | CountryController@index |
| GET | `/api/v1/on-boarding` | CountryController@onBoarding |
| GET | `/api/v1/on-boarding-driver` | CountryController@onBoardingDriver |
| GET | `/api/v1/on-boarding-owner` | CountryController@onBoardingOwner |

### تحت `common` (بعضها auth:sanctum)

| Method | URI | Controller | Middleware |
|--------|-----|------------|------------|
| GET | `/api/v1/common/modules` | CarMakeAndModelController@getAppModule | - |
| GET | `/api/v1/common/test-api` | CarMakeAndModelController@testApi | - |
| GET | `/api/v1/common/ride_modules` | CarMakeAndModelController@mobileAppMenu | - |
| GET | `/api/v1/common/goods-types` | GoodsTypesController@index | auth:sanctum |
| GET | `/api/v1/common/cancallation/reasons` | CancellationReasonsController@index | auth:sanctum |
| GET | `/api/v1/common/faq/list/{lat}/{lng}` | FaqController@index | auth:sanctum |
| GET | `/api/v1/common/sos/list/{lat}/{lng}` | SosController@index | auth:sanctum |
| POST | `/api/v1/common/sos/store` | SosController@store | auth:sanctum |
| POST | `/api/v1/common/sos/delete/{sos}` | SosController@delete | auth:sanctum |
| GET | `/api/v1/common/ticket-titles` | SupportTicketController@index | auth:sanctum |
| POST | `/api/v1/common/make-ticket` | SupportTicketController@makeTicket | auth:sanctum |
| POST | `/api/v1/common/reply-message/{supportTicket}` | SupportTicketController@replyMessage | auth:sanctum |
| GET | `/api/v1/common/view-ticket/{supportTicket}` | SupportTicketController@viewTicketDetails | auth:sanctum |
| GET | `/api/v1/common/list` | SupportTicketController@tikcetList | auth:sanctum |
| GET | `/api/v1/common/preferences` | PreferenceController@index | auth:sanctum |
| POST | `/api/v1/common/preferences/store` | PreferenceController@update | auth:sanctum |
| GET | `/api/v1/common/referral/progress` | ReferralController@progress | auth:sanctum |
| GET | `/api/v1/common/referral/history` | ReferralController@history | auth:sanctum |
| GET | `/api/v1/common/referral/referral-condition` | ReferralController@referralCondition | auth:sanctum |
| GET | `/api/v1/common/referral/driver-referral-condition` | ReferralController@driverReferralCondition | auth:sanctum |
| GET | `/api/v1/common/mobile/privacy` | LandingQuickLinkController@showPrivacyPage | - |
| GET | `/api/v1/common/mobile/terms` | LandingQuickLinkController@showTermsPage | - |

### أنواع المركبات `types`

| Method | URI | Controller |
|--------|-----|------------|
| GET | `/api/v1/types/{service_location}` | VehicleTypeController@getVehicleTypesByServiceLocation |
| GET | `/api/v1/types/sub-vehicle/{service_location}` | VehicleTypeController@getSubVehicleTypesByServiceLocation |

### إشعارات `notifications` (auth:sanctum)

| Method | URI | Controller |
|--------|-----|------------|
| GET | `/api/v1/notifications/get-notification` | ShowNotificationController@getNotifications |
| ANY | `/api/v1/notifications/delete-notification/{notification}` | ShowNotificationController@deleteNotification |
| ANY | `/api/v1/notifications/delete-all-notification` | ShowNotificationController@deleteAllNotification |

---

## 3. مسارات `api/v1` من `routes/api/v1/auth.php`

### تسجيل الدخول و OTP

| Method | URI | Controller |
|--------|-----|------------|
| POST | `/api/v1/mobile-otp` | LoginController@mobileOtp |
| POST | `/api/v1/validate-otp` | LoginController@validateSmsOtp |
| POST | `/api/v1/user/login` | LoginController@loginUser |
| POST | `/api/v1/driver/login` | LoginController@loginDriver |
| POST | `/api/v1/logout` | LoginController@logout | auth:sanctum |
| POST | `/api/v1/reset-password` | PasswordResetController@validateUserMobileIsExistForForgetPassword |

### تسجيل مستخدم

| Method | URI | Controller |
|--------|-----|------------|
| POST | `/api/v1/user/register` | UserRegistrationController@register |
| POST | `/api/v1/user/validate-mobile` | UserRegistrationController@validateUserMobile |
| POST | `/api/v1/user/validate-mobile-for-login` | UserRegistrationController@validateUserMobileForLogin |
| POST | `/api/v1/user/update-password` | UserRegistrationController@updatePassword |
| POST | `/api/v1/driver/update-password` | DriverSignupController@updatePassword |
| POST | `/api/v1/driver/register` | DriverSignupController@register |
| POST | `/api/v1/driver/validate-mobile` | DriverSignupController@validateDriverMobile |
| POST | `/api/v1/driver/validate-mobile-for-login` | DriverSignupController@validateDriverMobileForLogin |
| POST | `/api/v1/user/register/send-otp` | UserRegistrationController@sendOTP |
| POST | `/api/v1/owner/register` | DriverSignupController@ownerRegister |
| POST | `/api/v1/update/user/referral` | ReferralController@updateUserReferral | auth:sanctum |
| POST | `/api/v1/update/driver/referral` | ReferralController@updateDriverReferral | auth:sanctum |
| GET | `/api/v1/get/referral` | ReferralController@index | auth:sanctum |
| POST | `/api/v1/send-mail-otp` | UserRegistrationController@sendMailOTP |
| POST | `/api/v1/validate-email-otp` | UserRegistrationController@validateEmailOTP |
| POST | `/api/v1/user/register/validate-otp` | UserRegistrationController@validateOTP |
| POST | `/api/v1/admin/register` | AdminRegistrationController@register |

### كلمة المرور `password`

| Method | URI | Controller |
|--------|-----|------------|
| POST | `/api/v1/password/forgot` | PasswordResetController@forgotPassword |
| POST | `/api/v1/password/validate-token` | PasswordResetController@validateToken |
| POST | `/api/v1/password/reset` | PasswordResetController@reset |

---

## 4. مسارات `api/v1` من `routes/api/v1/api.php` (Common)

| Method | URI | Controller | Middleware |
|--------|-----|------------|------------|
| GET | `/api/v1/translation/get` | TranslationController@index | - |
| GET | `/api/v1/translation-user/get` | TranslationController@userIndex | - |
| GET | `/api/v1/translation/list` | TranslationController@listLocalizations | - |
| GET | `/api/v1/servicelocation` | ServiceLocationController@index | auth:sanctum |

---

## 5. مسارات `api/v1/user` من `routes/api/v1/user.php` (كلها auth:sanctum)

| Method | URI | Controller |
|--------|-----|------------|
| GET | `/api/v1/user` | AccountController@me |
| POST | `/api/v1/user/profile` | ProfileController@updateProfile |
| POST | `/api/v1/user/driver-profile` | ProfileController@updateDriverProfile |
| POST | `/api/v1/user/update-my-lang` | ProfileController@updateMyLanguage |
| POST | `/api/v1/user/update-bank-info` | ProfileController@updateBankinfo |
| GET | `/api/v1/user/get-bank-info` | ProfileController@getBankInfo |
| GET | `/api/v1/user/list-favourite-location` | ProfileController@FavouriteLocationList |
| POST | `/api/v1/user/add-favourite-location` | ProfileController@addFavouriteLocation |
| GET | `/api/v1/user/delete-favourite-location/{favourite_location}` | ProfileController@deleteFavouriteLocation |
| POST | `/api/v1/user/delete-user-account` | ProfileController@userDeleteAccount |
| POST | `/api/v1/user/update-location` | ProfileController@updateLocation |
| GET | `/api/v1/user/download-invoice/{requestId}` | TripRequestController@mobileShareInvoice |

---

## 6. مسارات `api/v1/driver` من `routes/api/v1/driver.php` (كلها auth:sanctum)

| Method | URI | Controller |
|--------|-----|------------|
| GET | `/api/v1/driver/documents/needed` | DriverDocumentController@index |
| POST | `/api/v1/driver/upload/documents` | DriverDocumentController@uploadDocuments |
| POST | `/api/v1/driver/online-offline` | OnlineOfflineController@toggle |
| GET | `/api/v1/driver/diagnostic` | DriverDocumentController@diagnostics |
| GET | `/api/v1/driver/today-earnings` | EarningsController@index |
| GET | `/api/v1/driver/weekly-earnings` | EarningsController@weeklyEarnings |
| GET | `/api/v1/driver/earnings-report/{from_date}/{to_date}` | EarningsController@earningsReport |
| GET | `/api/v1/driver/history-report` | EarningsController@historyReport |
| POST | `/api/v1/driver/add-my-route-address` | OnlineOfflineController@addMyRouteAddress |
| POST | `/api/v1/driver/enable-my-route-booking` | OnlineOfflineController@enableMyRouteBooking |
| POST | `/api/v1/driver/update-price` | EarningsController@updatePrice |
| GET | `/api/v1/driver/new-earnings` | EarningsController@newEarnings |
| POST | `/api/v1/driver/earnings-by-date` | EarningsController@earningsByDate |
| GET | `/api/v1/driver/all-earnings` | EarningsController@allEarnings |
| GET | `/api/v1/driver/list_of_plans` | SubscriptionController@listOfSubscription |
| POST | `/api/v1/driver/subscribe` | SubscriptionController@addSubscription |
| GET | `/api/v1/driver/leader-board/trips` | EarningsController@leaderBoardTrips |
| GET | `/api/v1/driver/leader-board/earnings` | EarningsController@leaderBoardEarnings |
| GET | `/api/v1/driver/invoice-history` | EarningsController@invoiceHistory |
| GET | `/api/v1/driver/new-incentives` | IncentiveController@newIncentive |
| GET | `/api/v1/driver/week-incentives` | IncentiveController@weekIncentives |
| GET | `/api/v1/driver/list/bankinfo` | DriverDocumentController@listBankInfo |
| POST | `/api/v1/driver/update/bankinfo` | DriverDocumentController@updateBankinfoNew |
| GET | `/api/v1/driver/loyalty/history` | DriverLevelController@listLevel |
| GET | `/api/v1/driver/rewards/history` | DriverLevelController@listRewards |

---

## 7. مسارات `api/v1/owner` من `routes/api/v1/owner.php` (كلها auth:sanctum)

| Method | URI | Controller |
|--------|-----|------------|
| GET | `/api/v1/owner/list-fleets` | FleetController@index |
| GET | `/api/v1/owner/fleet/documents/needed` | FleetController@neededDocuments |
| GET | `/api/v1/owner/list-drivers` | FleetController@listDrivers |
| POST | `/api/v1/owner/assign-driver/{fleet}` | FleetController@assignDriver |
| POST | `/api/v1/owner/add-fleet` | FleetController@storeFleet |
| POST | `/api/v1/owner/update-fleet/{fleet}` | FleetController@updateFleet |
| POST | `/api/v1/owner/delete-fleet/{fleet}` | FleetController@deleteFleet |
| POST | `/api/v1/owner/add-drivers` | FleetDriversController@addDriver |
| GET | `/api/v1/owner/delete-driver/{driver}` | FleetDriversController@deleteDriver |
| POST | `/api/v1/owner/dashboard` | OwnerController@ownerDashboard |
| POST | `/api/v1/owner/fleet-dashboard` | OwnerController@fleetDashboard |
| POST | `/api/v1/owner/fleet-driver-dashboard` | OwnerController@fleetDriverDashboard |

---

## 8. مسارات `api/v1/dispatcher` من `routes/api/v1/dispatcher.php`

| Method | URI | Controller |
|--------|-----|------------|
| POST | `/api/v1/dispatcher/request/eta` | EtaController@eta |
| POST | `/api/v1/dispatcher/request/list_packages` | EtaController@listPackages |

---

## 9. مسارات `api/v1/request` من `routes/api/v1/request.php` (كلها auth:sanctum)

| Method | URI | Controller |
|--------|-----|------------|
| POST | `/api/v1/request/list-packages` | EtaController@listPackages |
| GET | `/api/v1/request/promocode-list` | PromoCodeController@index |
| POST | `/api/v1/request/create` | CreateRequestController@createRequest |
| POST | `/api/v1/request/delivery/create` | DeliveryCreateRequestController@createRequest |
| POST | `/api/v1/request/change-drop-location` | EtaController@changeDropLocation |
| POST | `/api/v1/request/cancel` | UserCancelRequestController@cancelRequest |
| POST | `/api/v1/request/respond-for-bid` | CreateRequestController@respondForBid |
| POST | `/api/v1/request/user/payment-method` | UserCancelRequestController@paymentMethod |
| POST | `/api/v1/request/user/payment-confirm` | UserCancelRequestController@userPaymentConfirm |
| POST | `/api/v1/request/user/driver-tip` | UserCancelRequestController@driverTip |
| POST | `/api/v1/request/eta` | EtaController@eta |
| POST | `/api/v1/request/serviceVerify` | EtaController@serviceVerify |
| POST | `/api/v1/request/list-recent-searches` | EtaController@recentSearches |
| GET | `/api/v1/request/get-directions` | EtaController@getDirections |
| POST | `/api/v1/request/create-instant-ride` | InstantRideController@createRequest |
| POST | `/api/v1/request/create-delivery-instant-ride` | InstantRideController@createDeliveryRequest |
| POST | `/api/v1/request/respond` | RequestAcceptRejectController@respondRequest |
| POST | `/api/v1/request/arrived` | DriverArrivedController@arrivedRequest |
| POST | `/api/v1/request/started` | DriverTripStartedController@tripStart |
| POST | `/api/v1/request/cancel/by-driver` | DriverCancelRequestController@cancelRequest |
| POST | `/api/v1/request/end` | DriverEndRequestController@endRequest |
| POST | `/api/v1/request/trip-meter` | DriverEndRequestController@tripMeterRideUpdate |
| POST | `/api/v1/request/upload-proof` | DriverDeliveryProofController@uploadDocument |
| POST | `/api/v1/request/payment-confirm` | DriverEndRequestController@paymentConfirm |
| POST | `/api/v1/request/payment-method` | DriverEndRequestController@paymentMethod |
| POST | `/api/v1/request/ready-to-pickup` | DriverTripStartedController@readyToPickup |
| POST | `/api/v1/request/stop-complete` | DriverEndRequestController@tripEndBystop |
| POST | `/api/v1/request/stop-otp-verify` | DriverEndRequestController@stopOtpVerify |
| POST | `/api/v1/request/additional-charge` | DriverEndRequestController@additionalChargeUpdate |
| GET | `/api/v1/request/history` | RequestHistoryController@index |
| GET | `/api/v1/request/history/{id}` | RequestHistoryController@getById |
| GET | `/api/v1/request/invoice/{requestmodel}` | RequestHistoryController@invoice |
| POST | `/api/v1/request/rating` | RatingsController@rateRequest |
| GET | `/api/v1/request/chat-history/{request}` | ChatController@history |
| POST | `/api/v1/request/send` | ChatController@send |
| POST | `/api/v1/request/seen` | ChatController@updateSeen |
| GET | `/api/v1/request/user-chat-history` | ChatController@initiateConversation |
| POST | `/api/v1/request/user-send-message` | ChatController@sendMessage |

---

## 10. مسارات `api/v1/payment` من `routes/api/v1/payment.php`

### تحت auth:sanctum (throttle:30,1)

| Method | URI | Controller |
|--------|-----|------------|
| GET | `/api/v1/payment/cards/list` | PaymentController@listCards |
| POST | `/api/v1/payment/cards/make-default` | PaymentController@makeDefaultCard |
| POST | `/api/v1/payment/cards/delete/{card}` | PaymentController@deleteCard |
| GET | `/api/v1/payment/wallet/history` | PaymentController@walletHistory |
| GET | `/api/v1/payment/wallet/withdrawal-requests` | PaymentController@withDrawalRequests |
| POST | `/api/v1/payment/wallet/request-for-withdrawal` | PaymentController@requestForWithdrawal |
| POST | `/api/v1/payment/wallet/transfer-money-from-wallet` | PaymentController@transferMoneyFromWallet |
| POST | `/api/v1/payment/wallet/convert-point-to-wallet` | PaymentController@transferCreditFromPoints |
| POST | `/api/v1/payment/stripe/create-setup-intent` | StripeController@createStripeIntent |
| POST | `/api/v1/payment/stripe/save-card` | StripeController@saveCard |
| POST | `/api/v1/payment/stripe/add-money-to-wallet` | StripeController@addMoneyToWalletByStripe |
| GET | `/api/v1/payment/orange` | OrangeController@makePayment |
| GET | `/api/v1/payment/mercadopago` | MercadoPagoController@makePayment |
| GET | `/api/v1/payment/bankily/authenticate` | BankilyController@authenticate |
| GET | `/api/v1/payment/bankily/refresh` | BankilyController@refresh |
| GET | `/api/v1/payment/bankily/payment` | BankilyController@payment |
| GET | `/api/v1/payment/bankily/status` | BankilyController@status |

### بدون auth (أو مجموعة مختلفة)

| Method | URI | Controller | Middleware |
|--------|-----|------------|------------|
| GET | `/api/v1/payment/gateway` | PaymentController@paymentGateways | auth:sanctum |
| GET | `/api/v1/payment/gateway-for-ride` | PaymentController@paymentGatewaysForRide | - |
| POST | `/api/v1/payment/stripe/listen-webhooks` | StripeController@listenWebHooks | - |

---

## ملخص الملفات المحمّلة

| الملف | المسارات |
|-------|----------|
| `routes/api.php` | 4 routes مباشرة + تحميل كل ملفات `api/v1` |
| `routes/api/v1/api.php` | ترجمة + servicelocation |
| `routes/api/v1/auth.php` | تسجيل دخول، تسجيل، OTP، كلمة مرور |
| `routes/api/v1/common.php` | دول، on-boarding، common، types، notifications |
| `routes/api/v1/dispatcher.php` | dispatcher request eta و list_packages |
| `routes/api/v1/driver.php` | كل مسارات السائق |
| `routes/api/v1/owner.php` | كل مسارات المالك/الأسطول |
| `routes/api/v1/payment.php` | بطاقات، محفظة، بوابات دفع |
| `routes/api/v1/request.php` | طلبات رحلات، إلغاء، دفع، محادثة، تقييم |
| `routes/api/v1/user.php` | بروفايل مستخدم، مواقع مفضلة، فواتير |

لا توجد مسارات API خارج مجلد `routes/api.php` و `routes/api/v1/`. مسارات الـ Install والـ Web منفصلة ولا تُحسب كـ API.
