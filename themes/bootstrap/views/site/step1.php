<div class='step1 margin-10' id="step1">
    <?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
        'id'=>'choice-form',
        'enableAjaxValidation'=>false,
    )); ?>

    <table class="table">
        <thead>
        <tr>
            <th class="text-left text-upper">Free Email</th>
            <th> </th>
            <th class="text-left text-upper">Premium Email</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>
                <ul class="nav nav-tabs nav-stacked text-left">
                    <li><i class="icon-chevron-right icon-white"></i> Account Type:  Free email</li>
                    <li><i class="icon-chevron-right icon-white"></i> Space : 10 MB </li>
                    <li><i class="icon-chevron-right icon-white"></i> SMTP : No </li>
                    <li><i class="icon-chevron-right icon-white"></i> Invalid after 2 weeks of inactivity </li>
                </ul>

            </td>
            <td> </td>
            <td>
                <ul class="nav nav-tabs nav-stacked text-left">
                    <li><i class="icon-chevron-right icon-white"></i> Account Type: Premium</li>
                    <li><i class="icon-chevron-right icon-white"></i> Space : 250 MB </li>
                    <li><i class="icon-chevron-right icon-white"></i> SMTP : Yes </li>
                    <li><i class="icon-chevron-right icon-white"></i> Valid up to : depend upon package </li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>
                <?php $this->widget('bootstrap.widgets.TbButton', array(
                    'buttonType'=>'submit',
                    'type'=>'success',
                    'label'=> 'SET UP FREE EMAIL!',
                    'htmlOptions' =>array(
                        'value'=>1,
                        'name'=>'account_type',
                    ),
                )); ?>
            </td>
            <td> </td>
            <td>
                <?php $this->widget('bootstrap.widgets.TbButton', array(
                    'buttonType'=>'submit',
                    'type'=>'warning',
                    'label'=> 'PREMIUM REGISTRATION',
                    'htmlOptions' =>array(
                        'value'=>2,
                        'name'=>'account_type',
                    ),
                )); ?>
            </td>
        </tr>
        </tbody>
    </table>
    <div class="form-actions"></div>

    <?php $this->endWidget(); ?>
</div>