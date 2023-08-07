<?php
$title = 'Tìm kiếm';

require 'class/Database.php';
require 'class/Game.php';
require 'class/Auth.php';
require 'inc/init.php';


$db = new Database();
$pdo = $db->getConnect();
$keyword = $_GET['keyword'];
//page
$game_per_page = 6;  

$page = $_GET['page'] ?? 1; //mặc định là 1
$sobanghi = Game::demSoBanGhiSearchGame($pdo, $keyword)->sobanghi;
$minpage = 1;
$maxpage = ceil($sobanghi / $game_per_page);

$limit = $game_per_page;
$offset = ($page - 1) * $game_per_page;

$data = Game::searchGame($pdo, $keyword, $limit, $offset);

if (isset($_GET['action']) && isset($_GET['gameid'])) {
    $action = $_GET['action'];
    $gameid = $_GET['gameid'];

    if ($action == 'addcart') {
        $game = GAME::getOneByID($pdo, $gameid); 
        if ($game) {
            $gameidCol = array_column($_SESSION['cart'], 'gameid');
            if (in_array($gameid, $gameidCol)) {
                $_SESSION['cart'][$gameid]['qty'] += 1;
            } else {
                $item = [
                    'gameid' => $gameid,
                    'qty' => 1
                ];
                $_SESSION['cart'][$gameid] = $item;
            }
        }
    }
}
?>

<?php require 'inc/header.php'; ?>

<div class="background-image min-vh-100">
    <div class="container pt-3"> <!--bao nguyên thân tất cả game-->
        <h2 style="font-weight: bold; color: white">TỪ KHOÁ TÌM KIẾM: <?=$_GET['keyword']?></h2>
        <p style="background-color: green; width: auto; height: 3px;"></p>

        <!-- nếu data ko rỗng thì -->
        <?php if($data != null):?>

            <div class="row container pb-5">
            <?php foreach($data as $game): ?>
                <div class="col-12 col-md-6 col-lg-4" style="margin-top: 25px;">
                    <div class="card" style="width: 25rem;">
                            <!-- 1 item click dc -->
                        <a href="game.php?id=<?=$game->id ?>"><div class="card-body box">
                            <img class="rounded img-fluid" src="image/<?=$game->image ?>"/>
                            <!-- name -->
                            <h5 class="card-title"><a style="color:black; text-decoration-line: none; font-weight: bold;" href="game.php?id=<?=$game->id ?>"><?= $game->name ?></a></h5>
                            
                            <div>
                                <?php $dstheloai = Game::getTheLoaiGame($pdo, $game->id); ?>
                                <?php 
                                        $string = '';
                                    ?>
                                <div class="container" style="height:50px;">
                                <img src="icons/genre.png"/>
                                <!-- xử lý hiển thị genre và dấu phẩy cuối -->
                                <?php 
                                    $counter = 0;
                                    foreach ($dstheloai as $theloai): ?>
                                        <?php if($counter == count($dstheloai)-1):?>
                                            <a class="" style="font-size: 9pt; margin-top: -15px; color:green; text-decoration-line: none;" href="game-theo-the-loai.php?genreid=<?=$theloai->GenreId ?>"><?=$theloai->Genre?></a>
                                        <?php else: ?>
                                            <a class="" style="font-size: 9pt; margin-top: -15px; color:green; text-decoration-line: none;" href="game-theo-the-loai.php?genreid=<?=$theloai->GenreId ?>"><?=$theloai->Genre?>, </a>
                                        <?php endif; ?>
                                <?php 
                                    $counter = $counter + 1;
                                    endforeach; 
                                ?>
                                </div>
                            </div>
                            <!-- giá -->
                            <p class="card-text" style="font-weight: bold;"><small>Giá: <?= number_format($game->price, 0, ',', '.') ?>đ</small></p>
                            <!-- đăng nhập mới thấy dc giỏ hàng -->
                            <?php if (isset($_SESSION['log_detail'])): ?>
                            <a style="margin-top: -10px; color:green;" href="index.php?action=addcart&gameid=<?= $game->id ?>">Thêm giỏ hàng</a>
                            <?php endif; ?>
                        </div></a>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
            <?php else: ?>
                <h2 style="color:white; text-align: center; font-weight: bold; padding-top: 120px; padding-bottom: 300px;">KHÔNG CÓ NỘI DUNG NÀO ĐƯỢC TÌM THẤY!</h2> 
            <?php endif; ?>
    </div>
    
<!-- Phân trang--> 

<div class="mt-4 text-center" style="padding-bottom: 2px;">
        <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-center">
                
                <li class="page-item"><a class="page-link <?=($page <= 1) ? 'disabled': ''; ?>" href = "game-theo-the-loai.php?genreid=<?=$genreid?>&page=<?= ($page >1) ? $page-1 : ''; ?>">Trang trước</a></li>

                <?php for($dem = 1; $dem <= $maxpage; $dem++) { ?>

                    <?php if ($page !== $dem) { ?>
                        <li class="page-item"><a class="page-link" href="game-theo-the-loai.php?genreid=<?=$genreid?>&page=<?=$dem; ?>"><?= $dem; ?></a></li>
                    <?php } else { ?>
                        <li class="page-item"><a class="page-link active"><?=$dem;?></a></li>
                    <?php } ?>
                <?php } ?>
                
                <li class="page-item"><a class="page-link <?=($page >= $maxpage) ? 'disabled' : ''; ?>" href = "game-theo-the-loai.php?genreid=<?=$genreid?>&page=<?= ($page < $maxpage) ? $page+1 : ''; ?>">Trang sau</a></li>
            </ul>
        </nav>
        <p class="text-white" style="font-weight: bold;">Trang <?=$page ?> trên <?=$maxpage?></p>
    </div>
</div>

<?php require 'inc/footer.php'; ?>