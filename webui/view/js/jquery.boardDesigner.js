/**
 * jQuery board designer script
 */
$(document).ready(function() {
    
    // define global variables
    var alpha = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P',
                 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
             
    // set home rows dimensions
    var homeRowCount = 2;
    var homeColCount = 8;

    // set minimum board dimensions
    var minRowCount = 8;
    var minColCount = homeColCount;

    // set maximum board dimensions
    var maxRowCount = alpha.length;
    var maxColCount = 20; 
    
    // set initial board values
    $('#rowCount').val(board_data ? board_data.row_count : minRowCount);
    $('#colCount').val(board_data ? board_data.col_count : minColCount);

    // set initial home locations
    $('#homeCol').val(board_data ? board_data.home_col : 1);
	
	$('#boardName').val(board_data ? board_data.board_name : '');
	
	var coords = board_data ? board_data.coords : [];
	console.log(coords);
    
    // draw board on initial values
    drawBoard();
    
    $('.board-control').on('input', function() { 
        if (
            $('#colCount').val() >= minColCount && $('#colCount').val() <= maxColCount
            && 
            $('#rowCount').val() >= minRowCount && $('#rowCount').val() <= maxRowCount
            &&
            $('#homeCol').val() >= 1
        ) {
            drawBoard();
        } else if ($('#colCount').val() < minColCount) {
            $('#colCount').val(minColCount);
        } else if ($('#rowCount').val() < minRowCount) {
            $('#rowCount').val(minRowCount);
        } else if ($('#homeCol').val() < 1) {
            $('#homeCol').val(1);
        } else if ($('#colCount').val() > maxColCount) {
            $('#colCount').val(maxColCount);
        } else if ($('#rowCount').val() > maxRowCount) {
            $('#rowCount').val(maxRowCount);
        }
    });
    
    $('.space-toggle').on('click', function() { 
		console.log('toggle clicked on ' + $(this).prop('name'));
        if ($(this).prop('checked')) {
            $(this).parent().addClass('normal');
        } else {
            $(this).parent().removeClass('normal');
        }
    });
	
	$('.space-toggle').prop('checked', true).parent().addClass('normal');
    
    function drawBoard() {
        
        var boardHtml = '';
        
        // draw all potential spaces first
        for (var y = $('#rowCount').val() - 1; y >= 0; y--) {
            
            boardHtml += '<tr>';
            
            for (var x = 0; x < $('#colCount').val(); x++) {
                
                //console.log($('#rowCount').val() - i + ', ' + (parseInt(j) + 1));
                
                var spaceIsBlack = y % 2 == 0 && x % 2 == 0 || y % 2 != 0 && x % 2 != 0;
                
                boardHtml 
                    += '<td class="' + (spaceIsBlack ? 'black' : 'white') + '">'
                        + '<span>' + (alpha[x] + (y + 1)) + '</span>'
                        + '<input type="checkbox" class="space-toggle" name="' 
                            + (x + 1) + '-' + (y + 1) + '-is-active">'
                        + '<select name="' + (x + 1) + '-' + (y + 1) + '-class-id">'
                            + '<option value="0">no piece</option>'
                            + '<option value="6">pawn</option>'
                            + '<option value="5">knight</option>'
							+ '<option value="4">bishop</option>'
                            + '<option value="3">rook</option>'
                            + '<option value="2">queen</option>'
                            + '<option value="1">king</option>'
                        + '</select>'
                        + '<select name="' + (x + 1) + '-' + (y + 1) + '-piece-color">'
                            + '<option value="white">white</option>'
                            + '<option value="black">black</option>'
                        + '</select>'
                    + '</td>';
                
            }
            
            boardHtml += '</tr>';
            
        }
        
        // set the generated html to the table
        $('#board').html(boardHtml);
        
        // place home pieces on the board
        //placeHomePieces(1, $('#homeCol').val(), 'white');
        //placeHomePieces($('#rowCount').val() - 1, $('#homeCol').val(), 'black');
        
    }
    
    function getSpaceJquerySelector(row, col) {
        var selector = 'tr:eq(' + ($('#rowCount').val() - row) + ') td:eq(' + (col - 1) + ')';
        //console.log('(' + row + ',' + col + '): ' + selector);
        return selector;
    }
    
    function placeHomePieces(row, col, color) {
        
        row = parseInt(row);
        col = parseInt(col);
        
        //console.log('absHomeCoord: ' + row + ', ' + col + ' (' + color + ')');
        //console.log('rowLoop: ' + (homeRowCount + parseInt(row) - 1));
        //console.log('colLoop: ' + (homeColCount + parseInt(col) - 1));
        
        for (var i = row; i <= homeRowCount + row - 1; i++) {
            
            for (var j = col; j <= homeColCount + col - 1; j++) {
                
                var selector = getSpaceJquerySelector(i, j);
                var pawnRow;
                var royalRow;
                var relativeRow = i - row + 1;
                var relativeCol = j - col + 1;
                
                //console.log('relativeRow: ' + relativeRow);
                //console.log('relativeCol: ' + relativeCol);
                
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
                $(selector + ' input').prop('checked', true);
                
            }
            
        }
        
    }
    
});