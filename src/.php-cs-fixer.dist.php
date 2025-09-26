<?php

declare(strict_types=1);

return new PhpCsFixer\Config()
    ->setRules([
        '@PSR12'                                  => true,
        'header_comment'                          => false,
        'line_ending'                             => false,  // Отключаем добавление новой строки в конце файла
        'single_blank_line_at_eof'                => false,
        // Убираем пустые строки между use для классов и функций
        //'blank_line_between_import_groups'        => false,
        // Удаление неиспользуемых импортов
        'no_unused_imports'                       => true,
        'no_alternative_syntax'                   => true,
        'echo_tag_syntax'                         => ['format' => 'short'], // Принудительно используем <?= вместо <?php echo
        'native_function_invocation'              => false,
        // Добавим запрет на использование определенных функций
        'function_to_constant'                    => true, // Заменяем `get_class()` на `::class`
        'no_trailing_whitespace'                  => true, // Запрещаем пробелы в конце строк
        'yoda_style'                              => false, // Запрещаем Йода-стиль
        'braces'                                  => false,
        // Включить выравнивание в массивах
        'binary_operator_spaces'                  => [
            'default'   => 'single_space',
            'operators' => ['=>' => 'align'],
        ],
        'no_extra_blank_lines'                    => [
            'tokens' => [
                'curly_brace_block', // Убирает лишние строки внутри фигурных скобок, включая методы
                'extra',
                'parenthesis_brace_block',
                'square_brace_block',
                'throw',
                'use',
            ],
        ],
        'control_structure_continuation_position' => true,  // Убираем пробелы вокруг скобок
        'array_syntax'                            => ['syntax' => 'short'],
        'method_argument_space'                   => [
            // Пробелы вокруг конкатенации строк
            'on_multiline'                     => 'ignore',
            'keep_multiple_spaces_after_comma' => false,
            'after_heredoc'                    => false,
        ],
        'no_spaces_inside_parenthesis'            => true, // Пробелы вокруг конкатенации строк

        // Enforce lowercase keywords
        'lowercase_keywords'                      => true,

        // Enforce lowercase constants
        'constant_case'                           => ['case' => 'lower'],

        // Control blank lines around methods
        'class_attributes_separation'             => [
            'elements' => [
                'method' => 'one',
            ],
        ],

        // Control blank lines after imports
        'blank_line_after_namespace'              => true,
        'blank_line_after_opening_tag'            => true,
        'single_line_after_imports'               => true,

        // Other rules as needed
        //'concat_space'                            => ['spacing' => 'one'],
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
                         ->in([
                             __DIR__ . '/config',
                             __DIR__ . '/src',
                         ])
                         ->name('*.php')
                         ->exclude(['templates', 'views'])
    )
    ->setRiskyAllowed(true)
    ->setLineEnding("\n");
