            <h1>Создать новую тему</h1>
                    <div class="comment-form">
                        <?php $this->widget('application.extensions.VExtension.widgets.VFormBuilderWidget', array('model'=>$form, 'elements'=>$form->getFormElements())); ?>
                    </div>

<script type="text/javascript">
    $(document).ready(function(){
        $("#forum").vforum_comments({
            'userId' : <?php echo Yii::app()->user->id ? Yii::app()->user->id : 'false'; ?>,
            'onAuthorize' : function () {Vauth.launch(); return false;}
        });
    });
</script>