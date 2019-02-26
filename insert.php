<?php

//insert.php

include('database_connection.php');

$form_data = json_decode(file_get_contents("php://input"));

$error = '';
$message = '';
$validation_error = '';
$city_name = '';
$occupation = '';

if($form_data->action == 'fetch_single_data')
{
	$query = "SELECT * FROM tbl_sample WHERE id='".$form_data->id."'";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		$output['city_name'] = $row['city_name'];
		$output['occupation'] = $row['occupation'];
	}
}
elseif($form_data->action == "Delete")
{
	$query = "
	DELETE FROM tbl_sample WHERE id='".$form_data->id."'
	";
	$statement = $connect->prepare($query);
	if($statement->execute())
	{
		$output['message'] = 'Data Deleted';
	}
}
else
{
	if(empty($form_data->city_name))
	{
		$error[] = 'City Name is Required';
	}
	else
	{
		$city_name = $form_data->city_name;
	}

	if(empty($form_data->occupation))
	{
		$error[] = 'Occupation is Required';
	}
	else
	{
		$occupation = $form_data->occupation;
	}

	if(empty($error))
	{
		if($form_data->action == 'Insert')
		{
			$data = array(
				':city_name'		=>	$city_name,
				':occupation'		=>	$occupation
			);
			$query = "
			INSERT INTO tbl_sample 
				(city_name, occupation) VALUES 
				(:city_name, :occupation)
			";
			$statement = $connect->prepare($query);
			if($statement->execute($data))
			{
				$message = 'Data Inserted';
			}
		}
		if($form_data->action == 'Edit')
		{
			$data = array(
				':city_name'	=>	$city_name,
				':occupation'	=>	$occupation,
				':id'			=>	$form_data->id
			);
			$query = "
			UPDATE tbl_sample 
			SET city_name = :city_name, occupation = :occupation 
			WHERE id = :id
			";

			$statement = $connect->prepare($query);
			if($statement->execute($data))
			{
				$message = 'Data Edited';
			}
		}
	}
	else
	{
		$validation_error = implode(", ", $error);
	}

	$output = array(
		'error'		=>	$validation_error,
		'message'	=>	$message
	);

}



echo json_encode($output);

?>