<div class="alert-upgrade">
    <?php
        echo "Current Version: <B>"._APP_VERSION."</B> ";
        echo "Ready for: <B>".APP_VERSION."</B>  ";
        echo '<a href="'.base_url("update?id=".CRYPTO_KEY).'">Run the update</a>';
    ?>
</div>
<style>

    .alert-upgrade{
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        width: 100%;
        height: 50px;
        line-height: 50px;
        text-align: center;
        background: orange;
        color: black;
        z-index: 1000001111;
    }

</style>
<script>



</script>