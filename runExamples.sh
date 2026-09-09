#!/usr/bin/env bash


set -e
# set -x

examples=()
examples+=('example/1_basic_usage_acceptable_input.php')
examples+=('example/2_basic_usage_bad_input.php')
examples+=('example/3_errors_returned_acceptable_input.php')
examples+=('example/4_errors_returned_bad_input.php')
examples+=('example/5_other_validator.php')
examples+=('example/6_array_of_type_as_items.php')
examples+=('example/7_array_of_type_as_root.php')
examples+=('example/8_open_api_descriptions.php')
examples+=('example/9_dto_validation.php')
examples+=('example/10_better_example.php')
examples+=('example/11_datatype_without_annotations.php')
examples+=('example/12_basic_example_for_doc.php')
examples+=('example/13_datatype_factory_trait.php')
examples+=('example/14_multi_level_example.php')

for example in "${examples[@]}"
do
   :
   echo "Running example $example"
   echo "========================"
   php $example
   example_exit_code=$?

   if [ "$example_exit_code" -ne "0" ]; then echo "Example [] failed";  exit "$example_exit_code"; fi
done

echo ""
echo "examples completed without problem"