<?php
$file = $_GET['file'];
if ((strpos($file, './app/') === 0 || strpos($file, './model/') === 0 || strpos($file, './install/') === 0) && is_file("../../".$file)) {
    unlink("../../".$file);
    echo "<script>alert('�ļ���ɾ���ɹ���ˢ�����ɽ��ҳ������������ɣ�');</script>";
    exit;
}
echo "<script>alert('�ļ���ɾ��ʧ�ܣ�ָ������');</script>";
exit;
?>
