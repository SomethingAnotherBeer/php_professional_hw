<?php
declare(strict_types=1);
namespace App\Factory\Request;

abstract class CliRequestFactory
{
    protected array $errors = [];

    protected array $input_params;


    public function __construct(array $input_params)
    {
        $this->input_params = array_map(fn(string $param_name) => trim($param_name), $input_params);
    }


    protected function validateRequired(array $required_params, array $expected_errors_msgs): void
    {
        foreach ($required_params as $required_param) {
            if (!isset($this->input_params[$required_param])) {
                $this->errors[] = $expected_errors_msgs[$required_param];
            }
        }
    }

    protected function prepareNumericParamsToFloat(array $numeric_param_list, array $expected_error_list): array
    {
        $prepared_float_params = [];

        foreach ($numeric_param_list as $numeric_param) {
            if (!$this->checkParamIsNumeric($numeric_param)) {
                $this->errors[] = $expected_error_list[$numeric_param];
            }
            else {
                $prepared_float_params[$numeric_param] = (float)$this->input_params[$numeric_param];
            }
        }
        return $prepared_float_params;
    }

    protected function prepareParamsToBoolean(array $boolean_param_list, array $expected_error_list): array
    {
        $prepared_boolean_params = [];
        $boolean_match =
            [
                "true" => true,
                "false" => false,
                "yes" => true,
                "no" => false,
            ];

        foreach ($boolean_param_list as $boolean_param_name) {
            if (!$this->checkParamIsBoolean($boolean_param_name)) {
                $this->errors[] = $expected_error_list[$boolean_param_name];
            }
            else {
                $prepared_boolean_params[$boolean_param_name] = $boolean_match[$boolean_param_name];
            }
        }

        return $prepared_boolean_params;
    }


    protected function checkParamIsIsset(string $param_name): bool
    {
        return isset($this->input_params[$param_name]);

    }

    protected function checkParamIsNumeric(string $param_name): bool
    {
        return is_numeric(trim($this->input_params[$param_name]));
    }

    protected function checkParamIsBoolean(string $param_name): bool
    {
        return "true" === $this->input_params[$param_name] ||
            "false" === $this->input_params[$param_name] ||
            "yes" === $this->input_params[$param_name] ||
            "no" === $this->input_params[$param_name]
        ;
    }


}