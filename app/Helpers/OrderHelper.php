<?php

if (!function_exists('generateOrderId')) {
    /**
     * Generate unique order ID starting with "Grihasth"
     *
     * @return string
     */
    function generateOrderId()
    {
        $uniqueCode = strtoupper(uniqid()); // Generate unique ID
        return 'Grihasth' . $uniqueCode;
    }
}

