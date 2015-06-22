<?php

/**
 * File: HtmlHelper
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * It is available through the world-wide-web at this URL:
 * http://involic.com/license.txt
 * If you are unable to obtain it through the world-wide-web,
 * please send an email to license@involic.com so
 * we can send you a copy immediately.
 *
 * PrestaAttributes additional attributes to PrestaShop product.
 * Add MPN and ISBN attributes
 *
 * @author      Involic <contacts@involic.com>
 * @copyright   Copyright (c) 2011-2015 by Involic (http://www.involic.com)
 * @license     http://involic.com/license.txt
 */
class HtmlHelper
{
    public static function blockStart($text, $icon = "", $class = "")
    {
        $text = L::t($text);
        if (CoreHelper::isPS16()) {
            $heading = "";
            if ($text) {
                $heading = "<div class='panel-heading'>".$text."</div>";
            }

            return "<div class='panel'>$heading<div class='panel-body'>";
        }

        if (!empty($icon)) {
            $icon = "<img src='$icon'/>";
        }

        $legend = "";
        if (!empty($icon) || !empty($text)) {
            $legend = "<legend>".$icon.$text."</legend>";
        }

        return "<fieldset class='$class'>$legend";
    }

    public static function blockEnd()
    {
        if (CoreHelper::isPS16()) {
            return "</div></div>";
        }
        return "</fieldset>";
    }

    public static function tabBlockStart($text)
    {
        $text = L::t($text);
        if (CoreHelper::isPS16()) {
            $heading = "";
            if ($text) {
                $heading = "<div class='panel-heading'>".$text."</div>";
            }

            return "$heading<div class='panel-body'>";
        }

        $output = "<h4>".$text."</h4>";
        $output.= CoreHelper::isOnlyPS15()?'<div class="separation"></div>':'<hr class="clear"/>';
        $output.= "<br/>";

        return $output;
    }

    public static function tabBlockEnd()
    {
        if (CoreHelper::isPS16()) {
            return "</div>";
        }

        return "";
    }


    public static function checkBoxList($name, $select, $data, $options = array())
    {
        $output = "";
        $baseId = strtolower($name) . '_';
        !is_array($select) && $select = array();
        $valueKey = isset($options['value']) ? $options['value'] : false;
        $labelKey = isset($options['label']) ? $options['label'] : false;
        foreach ($data as $enhKey => $enhValue) {
            if ($valueKey) {
                $enhKey = $enhValue[$valueKey];
            }
            if ($labelKey) {
                $enhValue = $enhValue[$labelKey];
            }
            $isSelected = in_array($enhKey, $select);
            $output .= "<tr>";
            $output .= "<td><input type='checkbox' id='{$baseId}{$enhKey}' name='{$name}[]' value='{$enhKey}' " . ($isSelected ? "checked='checked'" : "") . "></td>";
            $output .= "<td><label for='{$baseId}{$enhKey}'>{$enhValue}</label><br/></td>";
            $output .= "</tr>";
        }
        return $output;
    }

    /**
     * Build html select box
     *
     * @param string $name    name of dropdown
     * @param mixed  $select  selected value on dropdown
     * @param array  $data    list of all options
     * @param array  $options addition html options
     */
    public static function dropDownList($name, $select, $data, $options = array())
    {
        $output = "";
        $output .= "<select";
        $output .= " name='" . $name . "'";

        $isAddSelect   = false;
        $addSelectText = " -- " . L::t("Please Select") . " -- ";
        if (isset($options['addSelect'])) {
            $isAddSelect = true;
            if (is_string($options['addSelect'])) {
                $addSelectText = $options['addSelect'];
            }
            unset($options['addSelect']);
        }
        $keyInData = "label";
        if (isset($options['keyInData'])) {
            $keyInData = $options['keyInData'];
            unset($options['keyInData']);
        }

        foreach ($options as $key => $value) {
            if ($value) {
                $output .= " {$key}='" . $value . "'";
            } else {
                $output .= " {$key}";
            }
        }
        $output .= ">";

        if ($isAddSelect) {
            // Adding selecting promt
            $output .= "<option value=''>{$addSelectText}</option>";
        }
        foreach ($data as $selectKey => $selectElement) {
            $idToSet   = $selectKey;
            $textToSet = $selectElement;
            if (is_array($selectElement)) {
                if (isset($selectElement[$keyInData]) && isset($selectElement['id'])) {
                    $idToSet   = $selectElement['id'];
                    $textToSet = $selectElement[$keyInData];
                } else {
                    if (isset($selectElement['name']) && isset($selectElement['id'])) {
                        $idToSet   = $selectElement['id'];
                        $textToSet = $selectElement['name'];
                    } else {
                        // Array but not required format, skip it
                        continue;
                    }
                }
            }
            $isSelected = false;
            if (is_array($select)) {
                $isSelected = in_array($idToSet, $select) || isset($select[$idToSet]) ? true : false;
            } else {
                $isSelected = ($idToSet == $select);
            }
            $output .= "<option value='{$idToSet}'" . ($isSelected ? " selected='selected'" : "") . ">{$textToSet}</option>";
        }
        $output .= "</select>";
        return $output;
    }

    public static function inputText($name, $value = '', $options = array())
    {
        $output = "";
        $output .= "<input type='text' name='{$name}' value='{$value}'";
        foreach ($options as $key => $value) {
            $output .= " {$key}='" . $value . "'";
        }
        $output .= "/>";

        return $output;
    }

    public static function dropDownListWithGroup($name, $select, $data, $options = array())
    {
        $onlyElements = false;
        if (isset($options['onlyElements'])) {
            $onlyElements = (bool) $options['onlyElements'];
            unset($options['onlyElements']);
        }
        $output = "";
        if (!$onlyElements) {
            $output .= "<select";
            $output .= " name='" . $name . "'";
        }

        $isAddSelect   = false;
        $addSelectText = " -- " . L::t("Please Select") . " -- ";
        if (isset($options['addSelect'])) {
            $isAddSelect = true;
            if (is_string($options['addSelect'])) {
                $addSelectText = $options['addSelect'];
            }
            unset($options['addSelect']);
        }

        $groupKey = 'group';
        if (isset($options['groupKey'])) {
            $groupKey = $options['groupKey'];

            unset($options['groupKey']);
        }

        if (!$onlyElements) {
            foreach ($options as $key => $value) {
                $output .= " {$key}='" . $value . "'";
            }
            $output .= ">";
        }

        if ($isAddSelect) {
            // Adding selecting promt
            $output .= "<option value=''>{$addSelectText}</option>";
        }

        $dataSortedByGroup = array();
        if (!is_array($data)) {
            $data = array();
        }
        foreach ($data as $singleItem) {
            $group = 'no-group';
            if (isset($singleItem[$groupKey])) {
                $group = $singleItem[$groupKey];
            }
            if (!isset($dataSortedByGroup[$group])) {
                $dataSortedByGroup[$group] = array();
            }
            $dataSortedByGroup[$group][] = $singleItem;
        }

        foreach ($dataSortedByGroup as $groupKey => $valuesInGroup) {
            if ($groupKey != 'no-group') {
                $output .= "<optgroup label='{$groupKey}'>";
            }
            foreach ($valuesInGroup as $singleValue) {
                $isSelected = $singleValue['id'] == $select;
                $output .= "<option value='{$singleValue['id']}'" . ($isSelected ? " selected='selected'" : "") . ">{$singleValue['label']}</option>";
            }
            if ($groupKey != 'no-group') {
                $output .= "</optgroup>";
            }
        }
        if (!$onlyElements) {
            $output .= "</select>";
        }
        return $output;
    }

    /**
     * @param array  $categories
     * @param array  $current
     * @param int    $id_category
     * @param string $parentLabel
     *
     * @return array
     */
    public static function recurseCategoryArray($categories, $current, $id_category = 1, $parentLabel = "")
    {
        $list = array();

        if ($parentLabel != "") {
            $parentLabel .= ">";
        }
        $parentLabel .= htmlspecialchars(stripslashes($current['infos']['name']));

        $categoryArrayElement = array(
            'category_id' => (int) $id_category,
            'category_name' => htmlspecialchars(stripslashes($current['infos']['name'])),
            'level' => $current['infos']['level_depth'],
            'label' => str_repeat(".", $current['infos']['level_depth'] * 5) . stripslashes($current['infos']['name']),
            'category_path' => $parentLabel,
            'used' => false,
        );

        $list[] = $categoryArrayElement;

        if (isset($categories[$id_category])) {
            foreach (array_keys($categories[$id_category]) as $key) {
                $addedList = HtmlHelper::recurseCategoryArray($categories, $categories[$id_category][$key], $key, $parentLabel );
                foreach ($addedList as $newElem) {
                    $list[] = $newElem;
                }
            }
        }

        return $list;
    }

    public static function recurseCategory($categories, $current, $id_category = 1, $id_selected = 1)
    {
        $isSelected = false;
        if (is_array($id_selected)) {
            $isSelected = in_array($id_category, $id_selected);
        } else {
            $isSelected = ($id_selected == $id_category);
        }
        echo '<option value="' . $id_category . '"' . ($isSelected ? ' selected="selected"' : '') .
            ' level="'.$current['infos']['level_depth'].'" category-name="'.htmlspecialchars(stripslashes($current['infos']['name'])).'">' .
            str_repeat('&nbsp;', $current['infos']['level_depth'] * 5) . stripslashes($current['infos']['name']) . '</option>';
        if (isset($categories[$id_category])) {
            foreach (array_keys($categories[$id_category]) as $key) {
                HtmlHelper::recurseCategory($categories, $categories[$id_category][$key], $key, $id_selected );
            }
        }
    }
}