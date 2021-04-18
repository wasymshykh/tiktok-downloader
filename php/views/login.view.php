
<div class="login">

    <form action="" method="post" class="login-middle">
        <div class="login-logo">
            <img src="<?=URL?>/assets/img/logo.png">
        </div>

        <?php if (!empty($errors)): ?>
        <div class="field-error"><strong>Error<?=count($errors)>1?'s':''?>!</strong> <?php foreach ($errors as $error): ?><?=$error.'. '?><?php endforeach;?></div>
        <?php endif; ?>

        <div class="field-group">
            <label for="username" class="field-group-label"><i class="fas fa-user"></i> Username</label>
            <input type="text" name="username" id="username" class="field-group-input" value="<?=$_POST['username']??''?>">
        </div>
        <div class="field-group">
            <label for="password" class="field-group-label"><i class="fas fa-key"></i> Password</label>
            <input type="password" name="password" id="password" class="field-group-input" value="<?=$_POST['password']??''?>">
        </div>
        <div class="field-submit">
            <button type="submit" class="submit-btn"><i class="fas fa-lock"></i> Login</button>
        </div>
    </form>

</div>
