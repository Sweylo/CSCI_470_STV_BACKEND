<?php require('../view/default/header.php'); ?>

<script type="text/javascript" src="../../js/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="../../js/jquery.boardDesigner.js"></script>
<script type="text/javascript">
	var board_data = <?php echo isset($board_data) ? $board_data : 'null' ?>;
</script>

<h2>Board Designer</h2>

<?php echo isset($message) ? "<p>$message</p>" : null; ?>

<form method="post" action="./">
    
    <input type="hidden" name="action" value="add_board">
    
    <div>
        board name <input type="text" name="boardName" id="boardName">
    </div>

    <div>
        rows 
        <input type="number" name="rowCount" class="board-control" id="rowCount">
        columns 
        <input type="number" name="colCount" class="board-control" id="colCount">
    </div>
    
    <div>
        home col
        <input type="number" name="homeCol" class="board-control" accept=""id="homeCol">
    </div>

    <table id="board">

    </table>
    
    <input type="submit" value="save board">
    
</form>