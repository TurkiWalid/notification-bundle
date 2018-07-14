
(function() {

    // ajax request to mark a notification as seen
    function markAsSeen(e) {
        var xhttp = new XMLHttpRequest();
        var element = e.target;
        xhttp.onreadystatechange = function () {
            // on success
            if (xhttp.readyState == 4 && xhttp.status == 200) {
                // mark notification as seen
                element.parentNode.classList+= ' seen';
                // remove button
                element.remove();

                // decrease notification count
                var notificationCounter = document.getElementById('notificationCount');
                var notificationNumber = parseInt(notificationCounter.innerHTML);
                notificationNumber--;
                notificationCounter.innerHTML = notificationNumber.toString();
                if (notificationNumber === 0){

                    document.getElementById('bell-alarm').innerHTML="<i id='alarm-notif-check' class='fa fa-bell' aria-hidden='true'/>";
                }
            }
        };
        xhttp.open("POST", element.href, true);
        xhttp.send();
        //alert(element.href);
    };

    function markAllAsSeen(e) {
        var xhttp = new XMLHttpRequest();
        var element = e.target;
        xhttp.onreadystatechange = function () {
            // on success
            if (xhttp.readyState == 4 && xhttp.status == 200) {
                // add "seen" class for all notifications
                var notifications = document.getElementsByClassName('notification');
                for (var notification in notifications){

                    $(notification).find('#lab').classList += ' seen';

                }
                // remove action buttons
                var paras = document.getElementsByClassName('ajax-notification');
                while(paras[0]) {
                    paras[0].parentNode.removeChild(paras[0]);
                }
                // set notification count to 0
                var notificationCount = document.getElementById('notificationCount');
                notificationCount.innerHTML = '0';
                document.getElementById('bell-alarm').innerHTML="<i  id='alarm-notif-check' class='fa fa-bell' aria-hidden='true'/>";

            }
        };
        xhttp.open("POST", element.href, true);
        xhttp.send();
    };

    // mark as seen button handler
    var btns = document.getElementsByClassName('ajax-notification');
    Array.prototype.forEach.call(btns, function(btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            markAsSeen(e);
        });
    });

    /* var btns = document.getElementsByClassName('ajax-notification');
     for(var btn in btns){
         btn.addEventListener('click', function (e) {
             e.preventDefault();
             markAsSeen(e);
         });
     }
 */
    // mark all as seen button handler
    document.getElementById('notification-MarkAllAsSeen').addEventListener('click',function (e) {
        e.preventDefault();
        markAllAsSeen(e);
    });

    // mark all as seen button handler
    var links = document.getElementsByClassName('notif_link');
    Array.prototype.forEach.call(links, function(link) {
        link.addEventListener('click', function (e) {
            var xhttp = new XMLHttpRequest();
            var url = this.getAttribute("data-link");
            xhttp.open("POST", url, true);
            xhttp.send();
        });

    });

})();
