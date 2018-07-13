<?php
	function sendToClient($msg) {
		ob_clean();
		echo '
			<div class="card white">
				<div class="card-content">
					<span class="card-title" style="text-align: center">' . $msg . '</span>
				</div>
			</div>';
		ob_end_flush();
	}
?>
