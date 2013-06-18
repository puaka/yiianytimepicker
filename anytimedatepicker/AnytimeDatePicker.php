<?php
/**
 * @copyright Copyright (c) 2013-2015 Puaka Astro <puaka.astro@gmail.com>
 *
 * @version 2.0
 * @license LGPL 2.1
 *
 * {@link http://www.gnu.org/licenses/lgpl-2.1.txt}
 */

/**
 * Datetime picker using Anytime datetime picker http://www.ama3.com/anytime/
 *
 * Example:
 *
 * <code>
 * <?php $this->widget('application.extensions.anytimedatepicker.AnytimeDatePicker',
 * array('name'=>'html')); ?>
 * </code>
 *
 * This extension includes anytime picker libraries
 *
 * @author Puaka Astro <puaka.astro@gmail.com>
 */
class AnytimeDatePicker extends CInputWidget
{
    private $showTodayButton    =   false;
    private $showClearButton    =   false;
    private $clearButtonOptions =   array();
    private $todayButtonOptions =   array();
    private $useButton           =  false;

    public function __construct($owner=null)
    {
        parent::__construct($owner);
    }

    public function setUseButton($value)
    {
        if (!is_bool($value))
        {
            throw new CException(Yii::t('AnytimeDatePicker', 'useButton must be boolean'));
        }
        $this->useButton = $value;
    }

    public function setShowTodayButton($value)
    {
        if (!is_bool($value))
        {
            throw new CException(Yii::t('AnytimeDatePicker', 'showTodayButton must be boolean'));
        }
        $this->showTodayButton = $value;
    }

    public function setShowClearButton($value)
    {
        if (!is_bool($value))
        {
            throw new CException(Yii::t('AnytimeDatePicker', 'setShowClearButton must be boolean'));
        }
        $this->showClearButton = $value;
    }

    public function setClearButtonOptions($value)
    {
        if (!is_array($value))
        {
            throw new CException(Yii::t('AnytimeDatePicker', 'setClearButtonOptions must be array'));
        }
        $this->clearButtonOptions   =   $value;
    }

    public function setTodayButtonOptions($value)
    {
        if (!is_array($value))
        {
            throw new CException(Yii::t('AnytimeDatePicker', 'setTodayButtonOptions must be array'));
        }
        $this->todayButtonOptions   =   $value;
    }

    /**
     * Executes the widget.
     * This method registers all needed client scripts and renders
     * the text field.
     */
    public function run()
    {
        list($name, $id) = $this->resolveNameID();

        $baseDir    =   dirname(__FILE__);
        $assets     =   Yii::app()->getAssetManager()->publish($baseDir.DIRECTORY_SEPARATOR.'assets');

        $cs = Yii::app()->getClientScript();
        $cs->registerCoreScript('jquery');

        $cs->registerCssFile($assets.'/css/anytime.css');


        $cs->registerScriptFile($assets.'/js/anytime.js', CClientScript::POS_END);
        $cs->registerScriptFile($assets.'/js/anytimetz.js', CClientScript::POS_END);


        $this->htmlOptions['id']    =   $id;

        if($this->hasModel()) {
            $textinput  =   CHtml::activeTextField($this->model, $this->attribute, $this->htmlOptions);
        }
        else
        {
            $textinput = CHtml::textField($name, $this->value, $this->htmlOptions);
        }
        if($this->useButton)
        {
            $textinput  .=  '<span class="add-on"><i class="icon-calendar btn-picker"></i></span>';
        }

      $js =<<<EOP
var rangeFormat = "%b %e, %Y %l:%i %p";
var rangeConv = new AnyTime.Converter({format:rangeFormat});
var now = new Date(),todayBtn = document.getElementById("{$id}_adpdate_today"),clearBtn = document.getElementById("{$id}_adpdate_clear")
if($("#{$id}").parent().find(".btn-picker").length > 0) {
    $("#{$id}").parent().find(".btn-picker").click(function(e) {
        e.preventDefault();
        var pickerOpt = {
            baseYear: 2000,
            earliest: new Date(now.getFullYear(), now.getMonth(),now.getDate(),11,0,0),
            format: "%b %e, %Y %l:%i %p",
            latest: new Date(now.getFullYear() + 1,11,31,23,59,59)
        };
        $("#{$id}").AnyTime_noPicker().AnyTime_picker(pickerOpt).focus();
        return false;
    });
} else {
    $("#{$id}").AnyTime_picker({
        baseYear: 2000,
        earliest: new Date(now.getFullYear(), now.getMonth(),now.getDate(),11,0,0),
        format: "%b %e, %Y %l:%i %p",
        latest: new Date(now.getFullYear() + 1,11,31,23,59,59)
    });
}
if(todayBtn) {
    $(todayBtn).click( function(e) {
        $("#{$id}").val(rangeConv.format(new Date())).change();
        return false;
    });
    $(todayBtn).trigger('click');
}
if(clearBtn) {
    $(clearBtn).click( function(e) {
        $("#{$id}").val('').change();
        return false;
    });
}
if(document.getElementById("{$id}")) {
    if(parseInt($("#{$id}").val(),10) > 0) {
        var d = new Date(Date.parse($("#{$id}").val()));
        $("#{$id}").val(rangeConv.format(d)).change();
    }
}
EOP;
        $cs->registerScript('Yii.'.get_class($this).'#'.$id, $js, CClientScript::POS_READY);

        if($this->showTodayButton)
        {
            $tbutton_options    =   array(
                'class' => 'btn'
            );
            $today_label    =   'Today';
            if($this->todayButtonOptions)
            {
                $tbutton_options    =   array_merge_recursive($this->todayButtonOptions, $tbutton_options);
                if(isset($tbutton_options['label']))
                {
                    unset($tbutton_options['label']);
                }
                if(is_array($tbutton_options['class']))
                {
                    $tbutton_options['class']   =   implode(' ', $tbutton_options['class']);
                }
                if(isset($this->todayButtonOptions['label']) && $this->todayButtonOptions['label'])
                {
                    $today_label    =   $this->todayButtonOptions['label'];
                }
            }
            //do not allow id override!
            $tbutton_options['id']  =   $id.'_adpdate_today';
            $today_button   =   CHtml::button($today_label, $tbutton_options);
            $textinput  .=  ' '.$today_button;
        }

        if($this->showClearButton)
        {
            $cbutton_options    =   array(
                'class' => 'btn'
            );
            $clear_label    =   'Clear';
            if($this->clearButtonOptions)
            {
                $cbutton_options    =   array_merge_recursive($this->clearButtonOptions, $cbutton_options);
                if(isset($cbutton_options['label']))
                {
                    unset($cbutton_options['label']);
                }
                if(is_array($cbutton_options['class']))
                {
                    $cbutton_options['class']   =   implode(' ', $cbutton_options['class']);
                }
                if(isset($this->clearButtonOptions['label']) && $this->clearButtonOptions['label'])
                {
                    $clear_label    =   $this->clearButtonOptions['label'];
                }
            }
            //do not allow id override!
            $cbutton_options['id']  =   $id.'_adpdate_clear';
            $clear_button   =   CHtml::button($clear_label, $cbutton_options);
            $textinput  .=  ' '.$clear_button;
        }

        echo $textinput;
    }
}
