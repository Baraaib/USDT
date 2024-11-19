<?php
// تحقق من أن الطلب تم عبر POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // استقبال البيانات المرسلة من النموذج
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $subject = htmlspecialchars($_POST['subject']);
    $message = htmlspecialchars($_POST['message']);

    // التحقق من صحة البيانات
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        echo "يرجى ملء جميع الحقول.";
        exit;
    }

    // البريد الإلكتروني المستهدف
    $to = "cryptolike@cryptolike.store";

    // العنوان الكامل للبريد
    $fullSubject = "رسالة جديدة من: $name - $subject";

    // محتوى الرسالة
    $emailBody = "
    اسم المرسل: $name\n
    البريد الإلكتروني: $email\n
    الموضوع: $subject\n
    الرسالة:\n$message
    ";

    // الهيدر الخاص بالبريد
    $headers = "From: $email\r\n" .
               "Reply-To: $email\r\n" .
               "Content-Type: text/plain; charset=UTF-8\r\n";

    // إرسال البريد
    if (mail($to, $fullSubject, $emailBody, $headers)) {
        echo "تم إرسال الرسالة بنجاح! سنرد عليك في أقرب وقت ممكن.";
    } else {
        echo "عذرًا، حدث خطأ أثناء إرسال الرسالة. يرجى المحاولة لاحقًا.";
    }
} else {
    echo "طلب غير صالح.";
}
?>
