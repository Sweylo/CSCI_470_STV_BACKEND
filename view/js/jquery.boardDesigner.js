/**
 * jQuery board designer controls
 */
$(document).ready(function() {
    
    var alpha = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'];
    
    // set minimum dimensions
    var minRowCount = 8;
    var minColCount = 8;
    
    // set maximum dimensions
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
                    + '<span class="coord">' 
                    + alpha[i] + (j + 1) 
                    + '</span>'
                    + '</td>';
                
            }
            
            boardHtml += '</tr>';
            
        }
        
        // set the generated html to the table
        $('#board').html(boardHtml);
        
        // place home pieces on the board
        placeWhiteHomePieces($('#whiteHomeRow').val(), $('#whiteHomeCol').val());
        placeBlackHomePieces($('#blackHomeRow').val(), $('#blackHomeCol').val());
        
    }
    
    function placeWhiteHomePieces(row, col) {
        
    }
    
    function placeBlackHomePieces(row, col) {
        
    }
    
});