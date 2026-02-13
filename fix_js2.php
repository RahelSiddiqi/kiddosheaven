<?php
$content = file_get_contents('resources/views/admin/products/create.blade.php');

// Add the event listener after updateFileInput() in handleFiles
$search1 = "							div.querySelector('.remove-preview').addEventListener('click', function() {
								newImageMap.delete(this.dataset.id);
								div.remove();
								updateFileInput();
							});
							container.insertBefore(div, dropArea);";

$replace1 = "							div.querySelector('.remove-preview').addEventListener('click', function() {
								newImageMap.delete(this.dataset.id);
								div.remove();
								updateFileInput();
								updatePrimaryImageOptions();
							});

							// Handle primary image radio selection
							div.querySelector('input[name=primary_image_radio]').addEventListener('change', function() {
								document.querySelectorAll('#image-preview > div').forEach(el => {
									el.classList.remove('border-blue-500');
									el.classList.add('border-gray-200', 'dark:border-gray-700');
								});
								div.classList.remove('border-gray-200', 'dark:border-gray-700');
								div.classList.add('border-blue-500');
								document.getElementById('primary_image').value = fileId;
							});

							container.insertBefore(div, dropArea);
							updatePrimaryImageOptions();";

$result1 = str_replace($search1, $replace1, $content);

if ($result1 === $content) {
    echo "Step 1: No changes - string not found\n";
} else {
    $content = $result1;
    echo "Step 1: Done\n";
}

// Add the updatePrimaryImageOptions function after updateFileInput function
$search2 = "			function updateFileInput() {
					const dataTransfer = new DataTransfer();
					newImageMap.forEach(file => dataTransfer.items.add(file));
					input.files = dataTransfer.files;
				}";

$replace2 = "			function updateFileInput() {
					const dataTransfer = new DataTransfer();
					newImageMap.forEach(file => dataTransfer.items.add(file));
					input.files = dataTransfer.files;
				}

				function updatePrimaryImageOptions() {
					const section = document.getElementById('primary-image-section');
					const previewCount = document.querySelectorAll('#image-preview > div').length;
					if (previewCount > 0) {
						section.style.display = 'block';
					} else {
						section.style.display = 'none';
						document.getElementById('primary_image').value = '';
					}
				}";

$result2 = str_replace($search2, $replace2, $content);

if ($result2 === $content) {
    echo "Step 2: No changes - string not found\n";
} else {
    $content = $result2;
    echo "Step 2: Done\n";
}

file_put_contents('resources/views/admin/products/create.blade.php', $content);
echo "File saved\n";
