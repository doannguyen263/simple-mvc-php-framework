<?php
function selected($selected, $current = true, $display = true, $type = 'selected')
{
	if ((string) $selected === (string) $current) {
		$result = " $type='$type'";
	} else {
		$result = '';
	}

	if ($display) {
		echo $result;
	}

	return $result;
}

function getCurrentUser()
{
	// Kiểm tra xem người dùng đã đăng nhập hay chưa
	if (isset($_SESSION['user'])) {
		// Lấy thông tin người dùng từ session
		$user = $_SESSION['user'];

		// Trả về thông tin người dùng hiện tại
		return $user;
	}
	// Nếu không có người dùng đăng nhập, trả về null hoặc thông tin mặc định tùy thuộc vào yêu cầu của bạn
	return null;
}
function getUserCurrentID()
{
	return $_SESSION['user']['ID'];
}


function findCountMoreByPostId($array, $search_key, $return_value, $target_post_id) {
	// Sử dụng array_filter để lọc mảng theo điều kiện
	$filtered_array = array_filter($array, function($item) use ($target_post_id,$search_key) {
			return $item[$search_key] == $target_post_id;
	});

	// Nếu mảng lọc có phần tử, lấy giá trị count_more từ phần tử đầu tiên, ngược lại trả về null
	if (!empty($filtered_array)) {
			$found_item = reset($filtered_array); // Lấy phần tử đầu tiên của mảng lọc
			return $found_item[$return_value];
	} else {
			return 0; // Trả về null nếu không tìm thấy post_id trong mảng
	}
}