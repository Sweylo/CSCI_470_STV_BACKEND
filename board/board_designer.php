<?php require('../view/header.php'); ?>

<script type="text/javascript" src="../view/js/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="../view/js/jquery.boardDesigner.js"></script>

<p><a href='../'>&LT;&LT; back to main</a></p>

<form method="post" action="./">
    
    <input type="hidden" name="action" value="save_board">
    
    <div>
        board name <input type="text" name="boardName" id="board-name">
    </div>

    <div>
        rows 
        <input type="number" name="rowCount" class="board-control" id="rowCount">
        columns 
        <input type="number" name="colCount" class="board-control" id="colCount">
    </div>
    
    <div>
        white home row 
        <input type="number" name="whiteHomeRow" class="board-control" accept=""id="whiteHomeRow">
        black home row
        <input type="number" name="blackHomeRow" class="board-control" id="blackHomeRow">
    </div>
    
    <div>
        white home col
        <input type="number" name="whiteHomeCol" class="board-control" accept=""id="whiteHomeCol">
        black home col
        <input type="number" name="blackHomeCol" class="board-control" id="blackHomeCol">
    </div>

    <table id="board">

    </table>
    
    <input type="submit" value="save board">
    
</form>