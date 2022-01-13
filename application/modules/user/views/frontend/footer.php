<!-- jQuery 2.1.4 -->
<script src="<?= base_url("views/skin/backend/plugins/jQuery/jQuery-2.1.4.min.js") ?>"></script>
<!-- Bootstrap 3.3.5 -->
<script src="<?= base_url("views/skin/backend/bootstrap/js/bootstrap.min.js") ?>"></script>
<!-- AdminLTE App -->
<script src="<?= base_url("views/skin/backend/dist/js/app.js") ?>"></script>
<script src="<?=  base_url("views/skin/backend/plugins/select2/select2.full.min.js")?>"></script>


<script>

    var NSTemplateUIAnimation = {

        button: {

            set loading(selector){
                var text  = selector.text().trim();
                selector.attr("disabled",true);
                selector.html("<i class=\"fa fa-spinner fa-spin\" aria-hidden=\"true\"></i>&nbsp;&nbsp;"+text);
            },

            set success(selector) {
                var text  = selector.text().trim();
                selector.html(text);
                selector.html("<i class=\"btn-saving-cart fa fa-check\" aria-hidden=\"true\"></i>&nbsp;&nbsp;"+text);
                selector.addClass('bg-green');
                selector.attr("disabled",true);
            },
            set default(selector) {
                var text  = selector.text().trim();
                selector.html(text);
                selector.attr("disabled",false);
            },

            // selector.html('<i class="btn-saving-cart fa fa-check" aria-hidden="true"></i>&nbsp;&nbsp;<?=Translate::sprint("Mail Sent")?>&nbsp;&nbsp;');
        },

        buttonWithIcon: {

            set loading(selector){
                var text  = selector.html().trim();
                selector.html("<i class=\"fa fa-spinner fa-spin\" aria-hidden=\"true\"></i>&nbsp;&nbsp;"+text);
            },

            set success(selector) {
                var text  = selector.html().trim();
                selector.html(text);
            },
            set default(selector) {
                var text  = selector.html().trim();
                selector.html(text);
            },

        },


    };

</script>

<?php
echo TemplateManager::loadScripts();
?>

</body>
</html>

