# Zero Invoice

第三方電子發票整合套件。設計目標不是只包綠界，而是提供一個可擴充的 invoice provider 架構，讓綠界、藍新或其他電子發票服務可以用一致的入口建立各自的發票模組。

- 綠界電子發票文件：https://developers.ecpay.com.tw/#invoice
- 綠界 B2C API 文件：https://developers.ecpay.com.tw/7809/

## 設計方向

套件分成三層：

- `InvoiceClient`：統一入口，負責依 provider 與發票類型建立實作。
- `Invoices/{Provider}`：各第三方服務商的實作，例如 `ECPay`、未來可新增 `NewebPay`。
- `Requests/Parameters`：各 provider 自己的請求參數物件，避免不同服務商欄位互相污染。

目前已註冊的 provider：

```php
InvoiceObject::INVOICE_NAME_EC + InvoiceObject::B2C => Zero\Invoices\ECPay\ECB2CInvoice
```

未來新增藍新時，預期新增類似結構：

```text
src/
  Invoices/
    NewebPay/
      NewebPayInvoice.php
      NewebPayB2CInvoice.php
      Requests/
        Parameters/
```

然後在啟動時註冊：

```php
use Zero\InvoiceClient;
use Zero\InvoiceObject;
use Zero\Invoices\NewebPay\NewebPayB2CInvoice;

InvoiceClient::registerInvoice(
    InvoiceObject::INVOICE_NAME_NEWEBPAY,
    InvoiceObject::B2C,
    NewebPayB2CInvoice::class
);

$client = new InvoiceClient(InvoiceObject::INVOICE_NAME_NEWEBPAY, InvoiceObject::B2C);
$invoice = $client->createInvoice();
```

這樣 `InvoiceClient` 不需要一直新增 `createNewebPayInvoice()`、`createXXXInvoice()`，只要把 provider class 註冊進來即可。

## 安裝

目前尚未發布到 Packagist。開源使用時可先用 Git repository 方式安裝：

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/your-vendor/invoice.git"
        }
    ],
    "require": {
        "zero/invoice": "dev-main"
    }
}
```

本機開發：

```bash
composer install
```

## 設定

複製設定檔：

```bash
cp src/config.example.php src/config.php
```

再將 `src/config.php` 內的綠界參數換成正式或測試環境資料：

```php
'invoiceParameters' => [
    'MerchantID' => 2000132,
    'HashKey' => 'ejCk326UnaZWKisg',
    'HashIV' => 'q9jcZX8Ib9LM8wYk',
],
```

`config.example.php` 內預設為綠界測試環境。

## 綠界 B2C 使用範例

```php
use Zero\InvoiceClient;
use Zero\InvoiceObject;
use Zero\Invoices\ECPay\Requests\Parameters\Issue;
use Zero\Invoices\ECPay\Requests\Parameters\Query;

$client = new InvoiceClient(InvoiceObject::INVOICE_NAME_EC, InvoiceObject::B2C);
$invoice = $client->createInvoice();

$issue = new Issue();
$issue->Data->RelateNumber = 'INV' . date('YmdHis');
$issue->Data->CustomerID = '';
$issue->Data->CustomerIdentifier = '';
$issue->Data->CustomerName = '測試客戶';
$issue->Data->CustomerAddr = '台北市測試路 1 號';
$issue->Data->CustomerPhone = '';
$issue->Data->CustomerEmail = 'test@example.com';
$issue->Data->ClearanceMark = '';
$issue->Data->Print = '1';
$issue->Data->Donation = '0';
$issue->Data->LoveCode = '';
$issue->Data->CarrierType = '';
$issue->Data->CarrierNum = '';
$issue->Data->TaxType = '1';
$issue->Data->SpecialTaxType = '';
$issue->Data->SalesAmount = 100;
$issue->Data->InvoiceRemark = '測試發票';
$issue->Data->InvType = '07';
$issue->Data->Vat = '1';
$issue->Data->Items = [
    [
        'ItemSeq' => 1,
        'ItemName' => '測試商品',
        'ItemCount' => 1,
        'ItemWord' => '個',
        'ItemPrice' => 100,
        'ItemTaxType' => '1',
        'ItemAmount' => 100,
        'ItemRemark' => '',
    ],
];

$result = $invoice->createIssue($issue);

$query = new Query(['RelateNumber' => $issue->Data->RelateNumber]);
$issueResult = $invoice->getIssue($query);
```

舊寫法 `$client->createECInvoice()` 仍可使用，但新功能建議使用 `$client->createInvoice()`，讓不同 provider 入口一致。

`MerchantID` 與 `RqHeader.Timestamp` 會由套件依設定自動帶入；若有特殊需求，也可以在參數物件上自行指定。

## Provider 擴充規則

新增第三方服務商時，建議遵守以下規則：

1. 在 `src/Invoices/{Provider}` 建立 provider 專屬目錄。
2. Provider 共用邏輯放在 `{Provider}Invoice.php`，例如加密、簽章、HTTP transport、回應解析。
3. 發票類型實作放在 `{Provider}B2CInvoice.php` 或 `{Provider}B2BInvoice.php`。
4. Provider 專用參數放在 `{Provider}/Requests/Parameters`。
5. 用 `InvoiceClient::registerInvoice()` 註冊 provider/type 對應 class。
6. 對外呼叫統一走 `new InvoiceClient($provider, $type)` 與 `createInvoice()`。

這個規則可以讓每個第三方的 API 差異留在各自 provider 內，不會把綠界的 `MerchantID`、`HashKey`、`RqHeader` 等設計混到藍新或其他服務商。

## 目前支援狀態

綠界 B2C 目前已完成 SDK 端的模組化串接骨架：

- 開立發票：`createIssue`
- 作廢發票：`createInvalid`
- 開立折讓：`createAllowance`
- 線上開立折讓：`createAllowanceByCollegiate`
- 作廢折讓：`createAllowanceInvalid`
- 線上作廢折讓：`createAllowanceInvalidByCollegiate`
- 查詢發票：`getIssue`
- 查詢作廢發票：`getInvalid`
- 查詢折讓明細：`getAllowanceList`
- 查詢作廢折讓：`getAllowanceInvalid`
- 查詢字軌設定：`getInvoiceWordSetting`
- 手機條碼驗證：`isBarcode`
- 捐贈碼驗證：`isLoveCode`
- 統一編號驗證：`isCompanyNameByTaxId`

尚未完成的部分：

- 尚未使用真實綠界測試商店逐支 API 打通驗證。
- 查詢類 API 目前先提供通用 `Query` 參數物件，尚未拆成每支 API 專用 DTO。
- 尚未補完整單元測試與 mock HTTP 測試。
- 尚未發布 Packagist。

因此目前狀態是「程式端串接流程已完成整理，可進入測試環境驗證」，但還不能宣稱正式環境已完整驗收。

## 測試

```bash
composer install
vendor/bin/phpunit
```

目前至少可先執行 PHP 語法檢查：

```bash
php -l src/InvoiceClient.php
php -l src/Invoices/ECPay/ECInvoice.php
php -l src/Invoices/ECPay/ECB2CInvoice.php
```

## License

MIT
