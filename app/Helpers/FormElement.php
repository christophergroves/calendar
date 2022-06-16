<?php

namespace App\Helpers;

use Collective\Html\FormFacade as Form;
use Collective\Html\HtmlFacade as HTML;


class FormElement extends Form
{
    public static function select($field_name, $label, $value, $options, $errors, $required = false, $attributes = [], $link = [], $col_widths = ['label'=>'lg-4', 'control'=>'lg-14'])
    {
        $class_errors = $errors && $errors->has($field_name) ? ' form-error' : null;
        $attributes['class'] = array_key_exists('class', $attributes) ? $attributes['class'].$class_errors.' display-inline form-control' : $class_errors.' display-inline form-control';
        $attributes['id'] = array_key_exists('id', $attributes) ? $attributes['id'] : $field_name;
        $asterisk = $required ? '*' : null;

        echo '<div class="form-group">';
        echo Form::label($field_name, $label, ['class'=>'col-'.$col_widths['label'].' control-label']);
        echo '<div class="col-'.$col_widths['control'].'">';
        echo Form::select($field_name, $options, $value, $attributes);
        echo '<p class="display-inline form-asterisk">';
        echo $asterisk;
        echo '</p>';
        if (! empty($link)) {
            echo '<p class="display-inline form-error-info input-element-link">';
            if (array_key_exists('action', $link)) {
                $link['params'] = array_key_exists('params', $link) ? $link['params'] : null;
                echo HTML::linkAction($link['action'], $link['text'], $link['params'], ['class'=>'text-muted']);
            } else {
                echo HTML::link('#', $link['text'], ['class'=>'text-muted', 'id'=>$field_name.'_link']);
            }
            echo '</p>';
        }
        echo '<p class="display-inline form-error-info">';
        echo $errors ? $errors->first($field_name) : '';
        echo '</p>';
        echo '</div>';
        echo '</div>';
    }

    public static function text($field_name, $label, $value, $errors, $required = false, $attributes = [], $link = [], $col_widths = ['label'=>'lg-4', 'control'=>'lg-14'])
    {
        $class_errors = $errors && $errors->has($field_name) ? ' form-error' : null;
        $attributes['class'] = array_key_exists('class', $attributes) ? $attributes['class'].$class_errors.' display-inline form-control' : $class_errors.' display-inline form-control';
        $attributes['id'] = array_key_exists('id', $attributes) ? $attributes['id'] : $field_name;
        $asterisk = $required ? '*' : null;

        echo '<div class="form-group">';
        if (! $label) {
            echo '<label class="col-'.$col_widths['label'].' control-label"></label>';
        } else {
            echo Form::label($field_name, $label, ['class'=>'col-'.$col_widths['label'].' control-label']);
        }

        echo '<div class="col-'.$col_widths['control'].'">';
        echo Form::text($field_name, $value, $attributes);
        echo '<p class="display-inline form-asterisk">';
        echo $asterisk;
        echo '</p>';
        if (! empty($link)) {
            echo '<p class="display-inline form-error-info input-element-link">';
            if (array_key_exists('action', $link)) {
                $link['params'] = array_key_exists('params', $link) ? $link['params'] : null;
                echo HTML::linkAction($link['action'], $link['text'], $link['params'], ['class'=>'text-muted']);
            } else {
                echo HTML::link('#', $link['text'], ['class'=>'text-muted', 'id'=>$field_name.'_link']);
            }
            echo '</p>';
        }
        echo '<p class="display-inline form-error-info">';
        echo $errors ? $errors->first($field_name) : '';
        echo '</p>';
        echo '</div>';
        echo '</div>';
    }

    public static function textDatepicker($field_name, $label, $value, $errors, $required = false, $attributes = [], $col_widths = ['label'=>'lg-4', 'control'=>'lg-14'])
    {
        $class_errors = $errors && $errors->has($field_name) ? ' form-error' : null;

        $attributes['class'] = array_key_exists('class', $attributes) ? $attributes['class'].$class_errors.' display-inline form-control' : $class_errors.' display-inline form-control';
        $attributes['id'] = array_key_exists('id', $attributes) ? $attributes['id'] : $field_name;
        $attributes['form-group-class'] = array_key_exists('form-group-class', $attributes) ? $attributes['form-group-class'] : null;
        $asterisk = $required ? '*' : null;

        echo '<div class="form-group">';
        echo Form::label($field_name, $label, ['class'=>'col-'.$col_widths['label'].' control-label']);
        echo '<div class="col-'.$col_widths['control'].'">';
        echo '<div class="input-group float-left '.$attributes['form-group-class'].' ">';
        echo Form::text($field_name, $value, $attributes);
        echo '<span class="input-group-addon" id="'.$attributes['id'].'-btn" style="cursor:pointer;"><span class="glyphicon glyphicon-calendar"></span></span>';
        echo '</div>';
        echo '<p class="display-inline form-asterisk">';
        echo $asterisk;
        echo '</p>';
        echo '<p class="display-inline form-error-info">';
        echo $errors ? $errors->first($field_name) : '';
        echo '</p>';
        echo '</div>';
        echo '</div>';
    }

    public static function textarea($field_name, $label, $value, $errors, $required = false, $attributes = [], $col_widths = ['label'=>'lg-4', 'control'=>'lg-14'])
    {
        $class_errors = $errors && $errors->has($field_name) ? ' form-error' : null;
        $attributes['class'] = array_key_exists('class', $attributes) ? $attributes['class'].$class_errors.' display-inline form-control' : $class_errors.' display-inline form-control textarea';
        $attributes['id'] = array_key_exists('id', $attributes) ? $attributes['id'] : $field_name;
        $asterisk = $required ? '*' : null;

        echo '<div class="form-group">';
        echo Form::label($field_name, $label, ['class'=>'col-'.$col_widths['label'].' control-label']);
        echo '<div class="col-'.$col_widths['control'].'">';
        echo Form::textarea($field_name, $value, $attributes);
        echo '<p class="display-inline form-asterisk">';
        echo $asterisk;
        echo '</p>';
        echo '<p class="display-inline form-error-info">';
        echo $errors ? $errors->first($field_name) : '';
        echo '</p>';
        echo '</div>';
        echo '</div>';
    }

    public static function checkBox($field_name, $label, $checked, $attributes = [], $col_widths = ['label'=>4, 'control'=>14])
    {
        $attributes['class'] = array_key_exists('class', $attributes) ? $attributes['class'].' display-inline' : ' display-inline';
        $attributes['id'] = array_key_exists('id', $attributes) ? $attributes['id'] : $field_name;
        $value = 1;
        // $asterisk = $required ? '*':null;

        echo '<div class="form-group">';
        echo '<div class="col-lg-offset-'.$col_widths['label'].' col-lg-'.$col_widths['control'].'">';
        echo '<div class="checkbox">';
        echo '<label>';
        echo Form::checkbox($field_name, $value, $checked, $attributes);
        echo $label;
        echo '<label>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }

    // this would need a foreach loop on each of the radio options in a group (not finished!)
    public static function radioInline($field_name, $label, $checked, $attributes = [], $col_widths = ['label'=>4, 'control'=>14])
    {
        $attributes['class'] = array_key_exists('class', $attributes) ? $attributes['class'].' display-inline' : ' display-inline';
        $attributes['id'] = array_key_exists('id', $attributes) ? $attributes['id'] : $field_name;
        $value = 1;
        // $asterisk = $required ? '*':null;

        echo '<div class="form-group">';
        echo '<label class="col-lg-'.$col_widths['label'].'">';
        echo $label;
        echo '<label class="radio-inline">&nbsp;';
        echo '<label>';
        echo Form::radio($field_name, $value, $checked, $attributes);
        echo $value;
        echo '<label>';
        echo '</div>';
        echo '</label>';
        echo '</div>';
    }

    public static function saveCancelButtons($col_widths = ['label'=>4, 'control'=>14])
    {
        echo '<div class="form-group">';
        echo '<div class="col-lg-offset-'.$col_widths['label'].' col-lg-'.$col_widths['control'].'">';
        echo Form::submit('Save', ['class'=>'btn btn-primary btn-save']);
        echo Form::submit('Cancel', ['name'=>'cancel', 'class'=>'btn btn-default btn-cancel']);
        echo '</div>';
        echo '</div>';
    }

    public static function hRule($col_widths = ['label'=>4, 'control'=>12])
    {
        echo '<hr class="float-left width65 hr-sm">';
        echo '<div class="clear-float"></div>';
    }

    public static function selectInline($field_name, $label, $value, $options, $errors, $required = false, $attributes = [], $col_widths = ['label'=>4, 'control'=>4])
    {
        $class_errors = $errors && $errors->has($field_name) ? ' form-error' : null;
        $attributes['class'] = array_key_exists('class', $attributes) ? $attributes['class'].$class_errors.' display-inline form-control' : $class_errors.' display-inline form-control';
        $attributes['id'] = array_key_exists('id', $attributes) ? $attributes['id'] : $field_name;
        $asterisk = $required ? '*' : null;

        // echo '<div class="col-lg-24">';

        echo '<div class="form-group">';
        if ($label) {
            echo Form::label($field_name, $label, ['class'=>' control-label']);
        }
        // echo Form::label($field_name,$label,array('class'=>'col-md-'.$col_widths['label'].' control-label'));

        // echo '<div class="col-md-'.$col_widths['control'].'">';

        echo Form::select($field_name, $options, $value, $attributes);
        // echo '<p class="display-inline form-asterisk">';echo $asterisk;echo '</p>';
        // echo '<p class="display-inline form-error-info">';echo $errors->first($field_name);echo '</p>';
        // echo '</div>';
        echo '</div>';

        // echo '</div>';
    }

    public static function textInline($field_name, $label, $value, $errors, $required = false, $attributes = [])
    {
        $class_errors = $errors && $errors->has($field_name) ? ' form-error' : null;
        $attributes['class'] = array_key_exists('class', $attributes) ? $attributes['class'].$class_errors.' display-inline form-control' : $class_errors.' display-inline form-control';
        $attributes['id'] = array_key_exists('id', $attributes) ? $attributes['id'] : $field_name;
        $asterisk = $required ? '*' : null;

        echo '<div class="form-group">';

        if ($label) {
            echo Form::label($field_name, $label, ['class'=>'display-block control-label']);
        }

        // echo '<div class="col-lg-'.$col_widths['control'].'">';

        // echo Form::label($field_name,$label,array('class'=>'control-label'));
        echo Form::text($field_name, $value, $attributes);
        // echo '<p class="display-inline form-asterisk">';echo $asterisk;echo '</p>';
        // echo '<p class="display-inline form-error-info">';echo $errors->first($field_name);echo '</p>';
        // echo '</div>';
        echo '</div>';
    }

    public static function textInlineDatepicker1($field_name, $label, $value, $errors, $required = false, $attributes = [])
    {
        $class_errors = $errors && $errors->has($field_name) ? ' form-error' : null;
        $attributes['class'] = array_key_exists('class', $attributes) ? $attributes['class'].$class_errors.' display-inline form-control' : $class_errors.' display-inline form-control';
        $attributes['id'] = array_key_exists('id', $attributes) ? $attributes['id'] : $field_name;
        $asterisk = $required ? '*' : null;

        echo '<div class="form-group">';
        echo Form::label($field_name, $label.'&nbsp;', ['class'=>'display-inline control-label']);
        echo '<div class="input-group">';
        echo Form::text($field_name, $value, $attributes);
        echo '<span class="input-group-addon" id="'.$attributes['id'].'-btn" style="cursor:pointer;"><span class="glyphicon glyphicon-calendar"></span></span>';
        echo '</div>';
        echo '<p class="display-inline form-asterisk">';
        echo $asterisk;
        echo '</p>';
        echo '<p class="display-inline form-error-info">';
        echo $errors ? $errors->first($field_name) : '';
        echo '</p>';
        echo '</div>';
    }

    public static function textInlineDatepicker($field_name, $label, $value, $errors, $required = false, $attributes = [], $col_widths = ['label'=>4, 'control'=>4])
    {
        $class_errors = $errors && $errors->has($field_name) ? ' form-error' : null;
        $attributes['class'] = array_key_exists('class', $attributes) ? $attributes['class'].$class_errors.' display-inline form-control' : $class_errors.' display-inline form-control';
        $attributes['id'] = array_key_exists('id', $attributes) ? $attributes['id'] : $field_name;
        $asterisk = $required ? '*' : null;

        echo '<div class="form-group">';
        echo Form::label($field_name, $label.'&nbsp;', ['class'=>'display-block control-label']);
        // echo Form::label($field_name,$label,array('class'=>'col-md-'.$col_widths['label'].' control-label'));
        // echo '<div class="col-md-'.$col_widths['control'].'">';
        echo '<div class="input-group">';
        echo Form::text($field_name, $value, $attributes);
        echo '<span class="input-group-addon" id="'.$attributes['id'].'-btn" style="cursor:pointer;"><span class="glyphicon glyphicon-calendar"></span></span>';
        // echo '</div>';
        echo '</div>';

        // echo '<p class="display-inline form-error-info">';echo $errors->first($field_name);echo '</p>';
        echo '</div>';
        // echo '<p class="display-inline form-asterisk">';echo $asterisk;echo '</p>';
    }

    public static function selectTextAfter($field_name, $label, $value, $options, $errors, $required = false, $attributes = [], $text = [], $col_widths = ['label'=>'md-4', 'control'=>'md-14'])
    {
        $class_errors = $errors && $errors->has($field_name) ? ' form-error' : null;
        $attributes['class'] = array_key_exists('class', $attributes) ? $attributes['class'].$class_errors.' display-inline form-control' : $class_errors.' display-inline form-control';
        $attributes['id'] = array_key_exists('id', $attributes) ? $attributes['id'] : $field_name;
        $asterisk = $required ? '*' : null;

        echo '<div class="form-group">';
        echo Form::label($field_name, $label, ['class'=>'col-'.$col_widths['label'].' control-label']);
        echo '<div class="col-'.$col_widths['control'].'">';
        echo Form::select($field_name, $options, $value, $attributes);
        echo '<p class="display-inline form-asterisk">';
        echo $asterisk;
        echo '</p>';

        if (! empty($text)) {
            echo '<p class="display-inline text-bold '.$text['class'].'">';
            echo  $text['text'];
            echo '</p>';
        }

        echo '<p class="display-inline form-error-info">';
        echo $errors ? $errors->first($field_name) : '';
        echo '</p>';
        echo '</div>';
        echo '</div>';
    }

    // <div class="form-group">
 //    <label class="sr-only" for="exampleInputEmail2">Email address</label>
 //    <input type="email" class="form-control" id="exampleInputEmail2" placeholder="Enter email">
 //  </div>
}
