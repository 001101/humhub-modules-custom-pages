<?php

use humhub\modules\custom_pages\models\ContainerPage;
use humhub\modules\custom_pages\widgets\PageIconSelect;
use humhub\widgets\Link;
use humhub\libs\Html;
use humhub\modules\custom_pages\helpers\Url;
use yii\widgets\ActiveForm;
use humhub\modules\custom_pages\models\Page;
use humhub\widgets\Button;
use humhub\modules\custom_pages\models\TemplateType;
use humhub\modules\content\widgets\richtext\RichTextField;

\humhub\modules\custom_pages\assets\Assets::register($this);
\humhub\modules\custom_pages\assets\CodeMirrorAssetBundle::register($this);

/** @var  $page \humhub\modules\custom_pages\models\CustomContentContainer */
/** @var  $subNav string */
/** @var  $pageType string */

$indexUrl = Url::to(['index', 'sguid' => null]);
$deleteUrl = Url::to(['delete', 'id' => $page->id, 'sguid' => null]);

$target = $page->getTargetModel();

$contentType = $page->getContentType();

?>
<div class="panel panel-default content-edit">
    <div class="panel-heading">
        <?= Yii::t('CustomPagesModule.base', '<strong>Custom</strong> Pages'); ?>
    </div>

    <?= $subNav ?>

    <div class="panel-body">
        <?= Button::back(Url::toChooseContentType($target, $pageType), Yii::t('CustomPagesModule.base', 'Back'))->sm(); ?>

        <h4><?= Yii::t('CustomPagesModule.views_common_edit', 'Configuration'); ?></h4>

        <div class="help-block">
            <?= Yii::t('CustomPagesModule.views_common_edit', 'Here you can configure the general settings of your {pageLabel}.', ['pageLabel' => $page->getLabel()]) ?>
        </div>

        <?php $form = ActiveForm::begin(['enableClientValidation' => false]); ?>
        <?php if ($page->isAllowedField('title')) : ?>
            <?= $form->field($page, 'title') ?>
        <?php endif; ?>

        <div class="form-group">
            <?= Html::textInput('type', $contentType->getLabel(), ['class' => 'form-control', 'disabled' => '1']); ?>
        </div>

        <div class="form-group">
            <?= Html::textInput('target', $target->name, ['class' => 'form-control', 'disabled' => '1']); ?>
        </div>

        <?php if ($page->isAllowedField('abstract') && (($page instanceof ContainerPage) || version_compare(Yii::$app->version, '1.3.11', '>='))) : ?>
            <?= $form->field($page, 'abstract')->widget(RichTextField::class); ?>
            <div class="help-block">
                <?= Yii::t('CustomPagesModule.views_common_edit',
                    'The abstract will be used as stream entry content to promote the actual page. 
                        If no abstract is given or the page is only visible for admins, no stream entry will be created.') ?>
            </div>
        <?php endif; ?>

        <?= $page->getContentType()->renderFormField($form, $page); ?>

        <?php if ($page instanceof Page && $page->hasAttribute('url') && $page->isAllowedField('url')) : ?>
            <?= $form->field($page, 'url') ?>
            <div class="help-block">
                <?= Yii::t('CustomPagesModule.views_common_edit', 'By setting an url shortcut value, you can create a better readable url for your page. If <b>URL Rewriting</b> is enabled on your site, the value \'mypage\' will result in an url \'www.example.de/p/mypage\'.') ?>
            </div>
        <?php endif; ?>

        <?php if ($page->isAllowedField('icon')) : ?>
            <?= PageIconSelect::widget(['page' => $page]) ?>
        <?php endif; ?>

        <?php if ($page->isAllowedField('sort_order')) : ?>
            <?= $form->field($page, 'sort_order')->textInput(); ?>
        <?php endif; ?>

        <?php if ($page->isAllowedField('cssClass')) : ?>
            <?= $form->field($page, 'cssClass'); ?>
        <?php endif; ?>

        <?php if ($page->isAllowedField('admin_only')) : ?>
            <?php if ($page->hasAttribute('admin_only')) : ?>
                <?= $form->field($page, 'admin_only')->checkbox() ?>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($page->isAllowedField('in_new_window')) : ?>
            <?php if ($page->hasAttribute('in_new_window')) : ?>
                <?= $form->field($page, 'in_new_window')->checkbox() ?>
            <?php endif; ?>
        <?php endif; ?>

        <?= Button::save()->submit() ?>

        <?php if (!$page->isNewRecord) : ?>
            <?= Link::danger(Yii::t('CustomPagesModule.views_common_edit', 'Delete'))->post(Url::toDeletePage($page, $target->container))->pjax(false)->confirm() ?>
        <?php endif; ?>

        <?php if (TemplateType::isType($contentType) && !$page->isNewRecord): ?>
            <?= Button::success(Yii::t('CustomPagesModule.views_common_edit', 'Inline Editor'))->link(Url::toInlineEdit($page, $target->container))->right()->icon('fa-pencil') ?>
        <?php endif; ?>

<?= Html::script(<<<JS
    $(document).one("humhub:ready", function () {
        if (!$("#html_content").length) {
            return;
        }
        setTimeout(function () {
            CodeMirror.fromTextArea($("#html_content")[0], {
                mode: "text/html",
                lineNumbers: true,
                extraKeys: {"Ctrl-Space": "autocomplete"}
            })
        }, 60);
        }
    );
JS
); ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>