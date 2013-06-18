yiianytimepicker
================

anytime picker http://www.ama3.com/anytime/ for yii

Usage:
------
<code>
$this->widget('application.extensions.anytimedatepicker.AnytimeDatePicker', array(
    'model'             =>  $model,
    'attribute'         =>  'date_field_name',
    'showClearButton'   =>  true,   // show clear button
    'showTodayButton'   =>  false,  // show today button
    'clearButtonOptions'=>  array(
        'class' =>  'btn-danger',   //your css class
        'label' =>  Yii::t('global', 'Clear'),
    ),
    'todayButtonOptions'=>  array(
        'class' =>  'btn-info',
        'label' =>  Yii::t('global', 'Today'),
    ),
    'htmlOptions'   =>  array(
        'readonly'  =>  'readonly',
        'class'     =>  'span2',
    ),
));
</code>
