<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>【COACHTECHフリマ】取引完了のお知らせ</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f7f7f7; margin: 0; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; border-radius: 8px;">
        <p style="font-size: 16px; margin-bottom: 10px;">{{ $purchase->item->user->name }} 様</p>

        <p style="font-size: 14px; line-height: 1.5; margin-bottom: 10px;">いつもご利用ありがとうございます。</p>
        <p style="font-size: 14px; line-height: 1.5; margin-bottom: 20px;">
            以下の商品につきまして、購入者によって取引が完了いたしましたのでご連絡いたします。
        </p>

        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr>
                <td style="padding: 8px; font-weight: bold; border-bottom: 1px solid #ddd;">商品名:</td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ $purchase->item->name }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; font-weight: bold; border-bottom: 1px solid #ddd;">購入者:</td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ $purchase->user->name }} 様</td>
            </tr>
            <tr>
                <td style="padding: 8px; font-weight: bold; border-bottom: 1px solid #ddd;">取引完了日時:</td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ now()->format('Y年m月d日 H:i') }}</td>
            </tr>
        </table>

        <p style="font-size: 14px; line-height: 1.5; margin-bottom: 20px;">
            取引内容をご確認いただき、マイページより評価をお願いいたします。
        </p>
        <p style="text-align: center; margin-bottom: 20px;">
            <a href="{{ route('profile.show') }}"
                style="display: inline-block; padding: 12px 24px; background-color: #4CAF50; color: white; text-decoration: none; font-weight: bold; border-radius: 5px;">
                マイページで評価する
            </a>
        </p>

        <p style="font-size: 14px; line-height: 1.5;">今後ともよろしくお願いいたします。</p>

        <p style="font-size: 14px; line-height: 1.5; margin-top: 30px;">COACHTECHフリマ</p>
    </div>
</body>

</html>