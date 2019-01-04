
    <form action="<?= \ItForFree\SimpleMVC\Url::link('CMSLogin/login')?>" method="post" style="width: 50%;">
        <input type="hidden" name="login" value="true" />

        <?php if ( !empty( $_GET['auth'] ) ) { ?>
                <div class="errorMessage"><?php echo $errorMessage ?></div>
        <?php } ?>

        <ul>

            <li>
                <label for="username">Username</label>
                <input type="text" name="userName" id="username" placeholder="Your admin username" required autofocus maxlength="20" />
            </li>

            <li>
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Your admin password" required maxlength="20" />
            </li>

        </ul>

        <div class="buttons">
            <input type="submit" name="login" value="Войти" />
        </div>

    </form>
	  

