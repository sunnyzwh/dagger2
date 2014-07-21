<?php
$file = $_GET['file'];
if ((strpos($file, './app/') === 0 || strpos($file, './model/') === 0 || strpos($file, './install/') === 0) && is_file("../../".$file)) {
    unlink("../../".$file);
    echo "<script>alert('文件已删除成功，刷新生成结果页面可以重新生成！');</script>";
    exit;
}
echo "<script>alert('文件已删除失败，指定有误？');</script>";
exit;
?>
