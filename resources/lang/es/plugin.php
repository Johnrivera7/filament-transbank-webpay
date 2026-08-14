<?php

return [
    'page_title' => 'Transbank Webpay',
    'section_credentials' => 'Credenciales Webpay Plus',
    'section_credentials_help' => 'Integración = pruebas con tarjetas de Transbank. Producción = cobros reales (requiere API Key Secret aprobada).',
    'enabled' => 'Habilitado',
    'commerce_code' => 'Código de comercio',
    'commerce_code_help' => 'Producción: tu código real (ej. 5970…). Integración: 597055555532.',
    'api_key' => 'API Key Secret',
    'api_key_help' => 'En integración usa la API Key pública de Transbank. En producción, la secret entregada tras validar el comercio.',
    'environment' => 'Ambiente',
    'env_integration' => 'Integración (pruebas)',
    'env_production' => 'Producción',
    'save' => 'Guardar',
    'saved' => 'Credenciales Transbank guardadas',
    'brand_hint' => 'SDK oficial Transbank · Webpay Plus',
    'redirecting' => 'Redirigiendo a Webpay…',
];
