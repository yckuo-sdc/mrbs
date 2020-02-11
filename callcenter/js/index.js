function confirm_func(id,page) {
	var r = confirm("是否刪除?");
		if (r == true) {
			window.location.href="entry_edit.php?action=del&id="+id+"&page="+page;
		}							    
}

$( document ).ready(function() {

	document.domain = "tainan.gov.tw";
	$("#checkAll").click(function() {
	   if($("#checkAll").prop("checked")) {
		 $("input[name='id[]']").each(function() {
			 $(this).prop("checked", true);
			 $(".flip").attr("disabled", false);
			 //console.log("true"); 
		 });
	   } else {
		 $("input[name='id[]']").each(function() {
			 $(this).prop("checked", false);
			 $(".flip").attr("disabled",true);
			 //console.log("false"); 
		 });           
		}
	});	
	//checkbox打勾就enable input，取消打勾就disable input
	$(".box").click(function(){
		var $this = $(this).parents('tr');
        if( $this.find(".box").prop("checked") ) {
			$this.find(".flip").attr("disabled", false);
        } else {
			$this.find(".flip").attr("disabled", true);
        }
	})
});
