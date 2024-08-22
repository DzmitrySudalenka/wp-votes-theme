document.addEventListener('DOMContentLoaded', function() {
  var voteButtons = document.querySelectorAll('.vote-button');

  voteButtons.forEach(function(button) {
    button.addEventListener('click', function() {
      var postId = this.getAttribute('data-post-id');
      var voteType = this.getAttribute('data-vote-type');
      var pageUrl = window.location.href;
      var likeCount = this.parentElement.querySelector('.like-count');
      var dislikeCount = this.parentElement.querySelector('.dislike-count');

      var xhr = new XMLHttpRequest();
      xhr.open('POST', ajaxurl, true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

      xhr.onload = function() {
        if (xhr.status >= 200 && xhr.status < 400) {
          var response = JSON.parse(xhr.responseText);
          if (response.success) {
            likeCount.textContent = response.data.likes;
            dislikeCount.textContent = response.data.dislikes;
          } else {
            alert('Error: ' + response.data);
          }
        } else {
          alert('Error: Could not reach the server.');
        }
      };

      xhr.send('action=post_vote&post_id=' + postId + '&vote_type=' + voteType + '&page_url=' + encodeURIComponent(pageUrl));
    });
  });
});
