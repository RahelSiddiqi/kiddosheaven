<?php
$content = file_get_contents('resources/views/admin/products/create.blade.php');

// Find and replace the handleFiles function
$search = "function handleFiles(files) {
					Array.from(files).forEach(file => {
						if (!file.type.startsWith('image/')) return;
						const fileId = Date.now().toString(36) + Math.random().toString(36).substr(2);
						newImageMap.set(fileId, file);
						const reader = new FileReader();
						reader.onload = function(e) {
							const div = document.createElement('div');
							div.className =
								'relative w-30 h-30 rounded-lg overflow-hidden border-2 border-gray-200 dark:border-gray-700';
							div.dataset.fileId = fileId;
							div.innerHTML = \`
								<img src=\"\${e.target.result}\" class=\"w-full h-full object-cover\">
								<div class=\"absolute bottom-1 left-1 bg-gray-800 text-white text-[10px] px-1.5 py-0.5 rounded\">New</div>
								<button type=\"button\" class=\"remove-preview absolute top-1 right-1 w-5 h-5 bg-red-600 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-700\" data-id=\"\${fileId}\">×</button>
							\`;
							div.querySelector('.remove-preview').addEventListener('click', function() {
								newImageMap.delete(this.dataset.id);
								div.remove();
								updateFileInput();
							});
							container.insertBefore(div, dropArea);
						};
						reader.readAsDataURL(file);
					});
					updateFileInput();
				}";

$replace = "function handleFiles(files) {
					Array.from(files).forEach(file => {
						if (!file.type.startsWith('image/')) return;
						const fileId = Date.now().toString(36) + Math.random().toString(36).substr(2);
						newImageMap.set(fileId, file);
						const reader = new FileReader();
						reader.onload = function(e) {
							const div = document.createElement('div');
							div.className =
								'relative w-30 h-30 rounded-lg overflow-hidden border-2 border-gray-200 dark:border-gray-700';
							div.dataset.fileId = fileId;
							div.innerHTML = \`
								<img src=\"\${e.target.result}\" class=\"w-full h-full object-cover\">
								<label class=\"absolute bottom-1 right-1 cursor-pointer bg-white/90 dark:bg-gray-800 rounded px-1\">
									<input type=\"radio\" name=\"primary_image_radio\" value=\"\${fileId}\" class=\"w-3 h-3 rounded border-gray-300 text-blue-600 focus:ring-blue-500/10\">
								</label>
								<div class=\"absolute bottom-1 left-1 bg-gray-800 text-white text-[10px] px-1.5 py-0.5 rounded\">New</div>
								<button type=\"button\" class=\"remove-preview absolute top-1 right-1 w-5 h-5 bg-red-600 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-700\" data-id=\"\${fileId}\">×</button>
							\`;
							div.querySelector('.remove-preview').addEventListener('click', function() {
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
							updatePrimaryImageOptions();
						};
						reader.readAsDataURL(file);
					});
					updateFileInput();
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

$result = str_replace($search, $replace, $content);

if ($result === $content) {
    echo "No changes made - string not found\n";
} else {
    file_put_contents('resources/views/admin/products/create.blade.php', $result);
    echo "Done - JavaScript updated\n";
}
