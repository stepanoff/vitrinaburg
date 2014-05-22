<?php
class VCbController extends Controller
{
    public function init ()
    {
        $res = parent::init();
        $this->layout = $this->getModule()->getLayout();
        return $res;
    }

    public function actionEdit()
    {
        /*
        $taskName = VCbModule::ROLE_CB_EDITOR;
        $user = VUser::model()->byLogin('admin')->find();
        $authManager = Yii::app()->authManager;
        $authManager->assign($taskName, $user->id);
        */
        if (!$this->isAdmin()) {
            throw new CHttpException(403);
        }
        $id = isset($_GET['id']) ? $_GET['id'] : false;
        if (!$id) {
            throw new CHttpException(404);
        }

        $cb = VContentBlock::model()->findByPk($id);
        if (!$cb) {
            throw new CHttpException(404);
        }

        if (isset($_POST['VContentBlock'])) {
            $cb->setAttributes($_POST['VContentBlock']);
            $cb->save();
        }

        $this->getModule()->registerBootstrapAssets();

        $view = $this->getModule()->getViewsAlias('edit');
        $this->render($view, array (
            'cb' => $cb,
        ));
    }

    public function isAdmin ()
    {
        $currentWebUser = Yii::app()->user;
        return $currentWebUser->checkAccess(VCbModule::ROLE_CB_EDITOR);
    }

}