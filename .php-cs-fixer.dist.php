<?php

declare(strict_types = 1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

$finder = (new Finder())
	->in(__DIR__ . '/src')
	->in(__DIR__ . '/tests');

return (new Config())
	->setParallelConfig(ParallelConfigFactory::detect())
	->setFormat('txt')
	->setUsingCache(true)
	->setCacheFile(__DIR__ . '/var/.php-cs-fixer/.cache')
	->setRiskyAllowed(true)
	->setFinder($finder)
	->setRules([
		'array_push' => true,
		'pow_to_exponentiation' => true,
		'no_alias_language_construct_call' => true,
		
		
		'array_syntax' => [
			'syntax' => 'short',
		],
		'no_whitespace_before_comma_in_array' => true,
		'normalize_index_brace' => true,
		'return_to_yield_from' => true,
		'trim_array_spaces' => true,
		
		
		'attribute_empty_parentheses' => true,
		
		
		'braces_position' => [
			'allow_single_line_empty_anonymous_classes' => false,
			'allow_single_line_anonymous_functions' => false,
		],
		'no_multiple_statements_per_line' => true,
		'no_trailing_comma_in_singleline' => true,
		'numeric_literal_separator' => [
			'strategy' => 'use_separator',
		],
		'single_line_empty_body' => true,
		
		
		'class_reference_name_casing' => true,
		'constant_case' => true,
		'lowercase_keywords' => true,
		'lowercase_static_reference' => true,
		'magic_constant_casing' => true,
		'magic_method_casing' => true,
		'native_function_casing' => true,
		'native_type_declaration_casing' => true,
		
		
		'cast_spaces' => true,
		'lowercase_cast' => true,
		'modernize_types_casting' => true,
		'no_short_bool_cast' => true,
		'no_unset_cast' => true,
		'short_scalar_cast' => true,
		
		
		'class_attributes_separation' => ['elements' => ['property' => 'one', 'const' => 'one', 'method' => 'one', ],],
		'class_definition' => true,
		'final_public_method_for_abstract_class' => true,
		'no_blank_lines_after_class_opening' => true,
		'no_unneeded_final_method' => true,
		'ordered_class_elements' => true,
		'ordered_interfaces' => [
			'direction' => 'ascend',
			'order' => 'alpha',
		],
		'ordered_types' => ['null_adjustment' => 'always_first',],
		'phpdoc_readonly_class_comment_to_keyword' => true,
		'protected_to_private' => true,
		'self_accessor' => true,
		'self_static_accessor' => true,
		'single_class_element_per_statement' => true,
		'single_trait_insert_per_statement' => true,
		'visibility_required' => true,
		
		
		'comment_to_phpdoc' => true,
		'no_empty_comment' => true,
		'no_trailing_whitespace_in_comment' => true,
		'single_line_comment_spacing' => true,
		'single_line_comment_style' => true,
		
		
		'native_constant_invocation' => true,
		
		
		'control_structure_braces' => true,
		'control_structure_continuation_position' => true,
		'elseif' => true,
		'include' => true,
		'no_break_comment' => true,
		'no_superfluous_elseif' => true,
		'no_unneeded_braces' => true,
		'no_unneeded_control_parentheses' => true,
		'no_useless_else' => true,
		'simplified_if_return' => true,
		'switch_case_semicolon_to_colon' => true,
		'switch_case_space' => true,
		'trailing_comma_in_multiline' => [
			'elements' => [
				'arguments',
				'arrays',
				'match',
				'parameters',
				'array_destructuring',
			],
		],
		'yoda_style' => true,
		
		
		'combine_nested_dirname' => true,
		'fopen_flag_order' => true,
		'fopen_flags' => true,
		'function_declaration' => [
			'closure_fn_spacing' => 'none',
			'closure_function_spacing' => 'none',
		],
		'implode_call' => true,
		'lambda_not_used_import' => true,
		'method_argument_space' => [
			'on_multiline' => 'ensure_fully_multiline',
			'attribute_placement' => 'standalone',
		],
		'native_function_invocation' => true,
		'no_spaces_after_function_name' => true,
		'no_unreachable_default_argument_value' => true,
		'no_useless_sprintf' => true,
		'nullable_type_declaration_for_default_null_value' => true,
		'return_type_declaration' => true,
		'static_lambda' => true,
		'void_return' => true,
		
		
		'global_namespace_import' => [
			'import_classes' => true,
			'import_constants' => true,
			'import_functions' => true,
		],
		'no_leading_import_slash' => true,
		'no_unneeded_import_alias' => true,
		'no_unused_imports' => true,
		'ordered_imports' => [
			'sort_algorithm' => 'alpha',
			'imports_order' => [
				'const',
				'class',
				'function',
			],
		],
		'single_import_per_statement' => true,
		'single_line_after_imports' => true,
		
		
		'combine_consecutive_issets' => true,
		'combine_consecutive_unsets' => true,
		'declare_equal_normalize' => [
			'space' => 'single',
		],
		'declare_parentheses' => true,
		'dir_constant' => true,
		'error_suppression' => [
			'mute_deprecation_error' => false,
			'noise_remaining_usages' => true,
		],
		'explicit_indirect_variable' => true,
		'function_to_constant' => [
			'functions' => [
				'get_called_class',
				'get_class',
				'get_class_this',
				'php_sapi_name',
				'phpversion',
				'pi',
			],
		],
		'get_class_to_class_keyword' => true,
		'is_null' => true,
		'nullable_type_declaration' => true,
		'single_space_around_construct' => [
			'constructs_preceded_by_a_single_space' => [
				'as',
				'else',
				'elseif',
				'use_lambda',
			],
		],
		
		
		'list_syntax' => [
			'syntax' => 'short',
		],
		
		
		'blank_line_after_namespace' => true,
		'blank_lines_before_namespace' => true,
		'clean_namespace' => true,
		'no_leading_namespace_whitespace' => true,
		
		
		'assign_null_coalescing_to_coalesce_equal' => true,
		'binary_operator_spaces' => true,
		'concat_space' => [
			'spacing' => 'one',
		],
		'logical_operators' => true,
		'long_to_shorthand_operator' => true,
		'new_with_parentheses' => true,
		'no_space_around_double_colon' => true,
		'no_useless_concat_operator' => true,
		'no_useless_nullsafe_operator' => true,
		'not_operator_with_successor_space' => true,
		'object_operator_without_whitespace' => true,
		'operator_linebreak' => true,
		'standardize_not_equals' => true,
		'ternary_operator_spaces' => true,
		'ternary_to_null_coalescing' => true,
		'unary_operator_spaces' => true,
		
		
		'blank_line_after_opening_tag' => true,
		'full_opening_tag' => true,
		'no_closing_tag' => true,
		
		
		'align_multiline_comment' => true,
		'no_blank_lines_after_phpdoc' => true,
		'no_empty_phpdoc' => true,
		'no_superfluous_phpdoc_tags' => [
			'allow_unused_params' => false,
			'remove_inheritdoc' => false,
			'allow_mixed' => true,
		],
		'phpdoc_align' => [
			'align' => 'left',
		],
		'phpdoc_array_type' => true,
		'phpdoc_indent' => true,
		'phpdoc_line_span' => true,
		'phpdoc_list_type' => true,
		'phpdoc_no_access' => true,
		'phpdoc_no_empty_return' => true,
		'phpdoc_no_package' => true,
		'phpdoc_order' => [
			'order' => [
				'param',
				'return',
				'throws',
			],
		],
		'phpdoc_param_order' => true,
		'phpdoc_return_self_reference' => true,
		'phpdoc_scalar' => true,
		'phpdoc_single_line_var_spacing' => true,
		'phpdoc_tag_casing' => true,
		'phpdoc_tag_type' => true,
		'phpdoc_trim' => true,
		'phpdoc_types' => true,
		'phpdoc_types_order' => [
			'null_adjustment' => 'always_first',
		],
		'phpdoc_var_annotation_correct_order' => true,
		'phpdoc_trim_consecutive_blank_line_separation' => true,
		
		
		'no_useless_return' => true,
		
		
		'multiline_whitespace_before_semicolons' => true,
		'no_empty_statement' => true,
		'no_singleline_whitespace_before_semicolons' => true,
		'semicolon_after_instruction' => true,
		
		
		'declare_strict_types' => true,
		'strict_comparison' => true,
		'strict_param' => true,
		
		
		'explicit_string_variable' => true,
		'no_binary_string' => true,
		'simple_to_complex_string_variable' => true,
		'single_quote' => [
			'strings_containing_single_quote_chars' => true,
		],
		'string_length_to_empty' => true,
		
		
		'array_indentation' => true,
		'blank_line_before_statement' => [
			'statements' => [
				'break',
				'continue',
				'declare',
				'default',
				'do',
				'exit',
				'for',
				'foreach',
				'goto',
				'if',
				'include',
				'include_once',
				'require',
				'require_once',
				'return',
				'switch',
				'throw',
				'try',
				'while',
				'yield',
				'yield_from',
			],
		],
		'blank_line_between_import_groups' => true,
		'compact_nullable_type_declaration' => true,
		'method_chaining_indentation' => true,
		'no_extra_blank_lines' => [
			'tokens' => [
				'attribute',
				'break',
				'case',
				'continue',
				'curly_brace_block',
				'default',
				'extra',
				'parenthesis_brace_block',
				'return',
				'square_brace_block',
				'switch',
				'throw',
				'use',
			],
		],
		'no_spaces_around_offset' => true,
		'no_trailing_whitespace' => true,
		'no_whitespace_in_blank_line' => true,
		'single_blank_line_at_eof' => true,
		'spaces_inside_parentheses' => true,
		'statement_indentation' => [
			'stick_comment_to_next_continuous_control_statement' => false,
		],
		'type_declaration_spaces' => true,
		'types_spaces' => true,
	]);
