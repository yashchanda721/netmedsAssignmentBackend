<?php

?>
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script>
    $(document).ready(function() {
        $.ajax({
            type: "get",
            url: "https://5f1a8228610bde0016fd2a74.mockapi.io/getTestList",
            dataType: 'json',
            success: function(response) {
                //console.log(response.type);
                $.ajax({
                    type: "post",
                    url: "http://localhost/netmedsAssignment/User/save_tests",
                    dataType: 'json',
                    data: {
                        'response': response
                    },
                    success: function(data) {
                        console.log(data);
                    }
                });
            }
        });
    });
</script>