/**
 * jQuery board designer controls
 */
$(document).ready(function() {
    
    var alpha = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'];
    
    // set home rows dimensions
    var homeRowCount = 2;
    var homeColCount = 8;
    
    // set minimum board dimensions
    var minRowCount = 8;
    var minColCount = homeColCount;
    
    // set maximum board dimensions
    var maxRowCount = alpha.length;
    var maxColCount = 20; 
    
    // set initial board dimensions
    $('#rowCount').val(minRowCount);
    $('#colCount').val(minColCount);
    
    // set initial home locations
    $('#whiteHomeRow').val(1);
    $('#whiteHomeCol').val(1);
    $('#blackHomeRow').val(7);
    $('#blackHomeCol').val(1);
    
    drawBoard();
    
    $('.board-control').on('input', function() { 
        if ($('#colCount').val() >= minColCount && $('#rowCount').val() >= minRowCount) {
            drawBoard();
        } else if ($('#colCount').val() < minColCount) {
            $('#colCount').val(minColCount);
        } else if ($('#rowCount').val() < minRowCount) {
            $('#rowCount').val(minRowCount);
        }
    });
    
    function drawBoard() {
        
        var boardHtml = '';
        
        // draw all potential spaces first
        for (var i = $('#rowCount').val() - 1; i >= 0; i--) {
            
            boardHtml += '<tr>';
            
            for (var j = 0; j < $('#colCount').val(); j++) {
                
                
                var isBlack = i % 2 == 0 && j % 2 == 0 || i % 2 != 0 && j % 2 != 0;
                
                boardHtml += '<td class="' + (isBlack ? 'black' : 'white') + '">';
                
                boardHtml += alpha[i] + (j + 1);
                
                boardHtml += '</td>';
                
                boardHtml 
                    += '<td class="' + isBlack ? 'black' : 'white' + '">'
                    + '<span class="coordX">' 
                    + alpha[i]
                    + '</span>'
                    + '<span class="coordY">'
                    + (j + 1) 
                    + '</span>'
                    + '</td>';
                
            }
            
            boardHtml += '</tr>';
            
        }
        
        // set the generated html to the table
        $('#board').html(boardHtml);
        
        // place home pieces on the board
        placeHomePieces($('#whiteHomeRow').val(), $('#whiteHomeCol').val(), 'white');
        placeHomePieces($('#blackHomeRow').val(), $('#blackHomeCol').val(), 'black');
        
    }
    
    function getSpaceJquerySelector(row, col) {
        var selector = 'tr:eq(' + ($('#rowCount').val() - row) + ') td:eq(' + (col - 1) + ')';
        console.log('(' + row + ',' + col + '): ' + selector);
        return selector;
    }
    
    function placeHomePieces(row, col, color) {
        
        row = parseInt(row);
        col = parseInt(col);
        
        console.log('absHomeCoord: ' + row + ', ' + col + ' (' + color + ')');
        //console.log('rowLoop: ' + (homeRowCount + parseInt(row) - 1));
        //console.log('colLoop: ' + (homeColCount + parseInt(col) - 1));
        
        for (var i = row; i <= homeRowCount + row - 1; i++) {
            
            for (var j = col; j <= homeColCount + col - 1; j++) {
                
                var selector = getSpaceJquerySelector(i, j);
                var pawnRow;
                var royalRow;
                var relativeRow = i - row + 1;
                var relativeCol = j - col + 1;
                
                console.log('relativeRow: ' + relativeRow);
                console.log('relativeCol: ' + relativeCol);
                
                if (color == 'black') {
                    pawnRow = 1;
                    royalRow = 2;
                } else if (color == 'white') {
                    pawnRow = 2;
                    royalRow = 1;
                }
                
                $(selector).addClass('normal');
                
                if ( // rook
                    relativeRow == royalRow && relativeCol == 1
                    ||
                    relativeRow == royalRow && relativeCol == 8
                ) {
                    $(selector).append('<p>rook</p>');

                } else if ( // bishop
                    relativeRow == royalRow && relativeCol == 3
                    ||
                    relativeRow == royalRow && relativeCol == 6
                ) {
                    $(selector).append('<p>bishop</p>');
                } else if ( // knight
                    relativeRow == royalRow && relativeCol == 2
                    ||
                    relativeRow == royalRow && relativeCol == 7
                ) {
                    $(selector).append('<p>knight</p>');
                } else if ( // queen
                    relativeRow == royalRow && relativeCol == 4
                ) {
                    $(selector).append('<p>queen</p>');
                } else if ( // king
                    relativeRow == royalRow && relativeCol == 5
                ) {
                    $(selector).append('<p>king</p>');
                } else if ( // pawn
                    relativeRow == pawnRow
                ) {
                    $(selector).append('<p>pawn</p>');
                }
                
                //$(selector).append('<input type="hidden" name="' + i + '-' + j + '">');
                $(selector).append('<input type="hidden" name="' 
                    + "{ 'row': " + i + ", 'col': " + j + " }" 
                    + '">');
                
            }
            
        }
        
    }
    
});