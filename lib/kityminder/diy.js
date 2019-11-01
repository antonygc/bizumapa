(function(){

	var oldData;
	var html = '';
	html += '<a class="diy export" data-type="json">导出json..</a>',
	html += '<a class="diy export" data-type="md">导出md</a>',
	html += '<a class="diy export" data-type="km">导出km</a>',
	html += '<button class="diy input">',
	html += '导入<input type="file" id="fileInput">',
	html += '</button>';

	$('.editor-title').append(html);

	$(document).on('mouseover', '.export', function(event) {
		// 链接在hover的时候生成对应数据到链接中
		event.preventDefault();
		var $this = $(this),
				type = $this.data('type'),
				exportType;
		switch(type){
			case 'km':
				exportType = 'json';
				break;
			case 'md':
				exportType = 'markdown';
				break;
			default:
				exportType = type;
				break;
		}
		if(JSON.stringify(oldData) == JSON.stringify(editor.minder.exportJson())){
			return;
		}else{
			oldData = editor.minder.exportJson();
		}

		editor.minder.exportData(exportType).then(function(content){
			switch(exportType){
				case 'json':
					console.log($.parseJSON(content));
					break;
				default:
					console.log(content);
					break;
			}
			$this.css('cursor', 'pointer');
			var blob = new Blob([content]),
					url = URL.createObjectURL(blob);
			var aLink = $this[0];
			aLink.href = url;
			aLink.download = $('#node_text1').text()+'.'+type;
		});
	}).on('mouseout', '.export', function(event) {
		// 鼠标移开是设置禁止点击状态，下次鼠标移入时需重新计算需要生成的文件
		event.preventDefault();
		$(this).css('cursor', 'not-allowed');
	}).on('click', '.export', function(event) {
		// 禁止点击状态下取消跳转
		var $this = $(this);
		if($this.css('cursor') == 'not-allowed'){
			event.preventDefault();
		}
	});



	angular.module('kityminderDemo', ['kityminderEditor'])
	.controller('MainController', function($scope) {
		$scope.initEditor = function(editor, minder) {
			window.editor = editor;
			window.minder = minder;
			window.minder.execCommand('template', 'right');

			editor.minder.importData('json', userData)
				.then(function(data){
					// console.log(data)
					$(fileInput).val('');
				});
		};
	});

})();
