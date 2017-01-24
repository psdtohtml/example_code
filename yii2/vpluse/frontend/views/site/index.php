<?php
/* @var $service string */
?>
<div class="content__section">
    <div class="info">
        <span class="title-block">
            <?php
            switch ($service) {
                case 'rebate' :
                    echo 'Сервис Rebate';
                    break;
                case 'vps' :
                    echo 'Vpluse-vps';
                    break;
                case 'bot' :
                    echo 'Vpluse-bot';
                    break;
                default : echo 'Сервис не выбран';
            }
            ?>
        </span>
    </div>
</div>
<?php
$this->registerJs("
    $(document).ready(function(){
        //защита от повторных регистраций
        localStorage.setItem('register', 'zzz');
    });
    ");
?>
