<?php

?>

<div class="candyshop-v1">

    <button id="load-candies" class="button">Load Candies!</button>

    <table id="candy-list" class="dokan-table dokan-table-striped product-listing-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Content</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <hr/>

    <form id="candy-create" method="POST">
        <input type="text" name="name" value="" placeholder="name..."/>
        <input type="text" name="details" value="" placeholder="details..."/>
        <input type="submit" class="button" value="Create Candy"/>
    </form>
</div>

<script type="text/javascript">
$(document).ready(function(){
    function update_candy_list(){
        var table = document.getElementById('candy-list').getElementsByTagName('tbody')[0];
        table.innerHTML = '';

        $.get( "/wp-json/candyshop/v1/candy", function( data ) {
            var d = data || [];
            d.forEach(function(el, idx){
                var row = table.insertRow(table.rows.length);
                var c1 = row.insertCell(0);
                var c2 = row.insertCell(1);
                var c3 = row.insertCell(2);
                var c4 = row.insertCell(3);
                var c5 = row.insertCell(4);
    
                c1.innerHTML = el.ID;
                c2.innerHTML = el.post_title;
                c3.innerHTML = el.post_content;
                c4.innerHTML = el.post_status;
                c5.innerHTML = el.post_date;
            });
        });
    }

    function create_candy(evt){
        evt.preventDefault();

        var $this = $(this);
        var query = {
            name: $this.find('input[name=name]').val(),
            details: $this.find('input[name=details]').val()
        };
        var posting = $.post('/wp-json/candyshop/v1/candy', query);

        posting.done(function(data){
            alert('Done!');
            update_candy_list();
        });
        posting.fail(function(){
            alert('Something wrong');
        });

        return false;
    }

    $("#load-candies").click(update_candy_list);
    $("#candy-create").submit(create_candy);
    update_candy_list();
});
</script>
