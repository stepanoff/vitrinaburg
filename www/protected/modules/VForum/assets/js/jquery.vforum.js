	(function($){
		$.fn.vforum_comments = function(opts){
		opts = $.extend({}, $.fn.vforum_comments.defaults, opts);
			return this.each(function(){
				$.fn.vforum_comments.instances[$(this).attr('id')] = new VForumComments(this, opts, $(this).attr('id') );
				return $.fn.vforum_comments.instances[$(this).attr('id')];
			});
		};

		$.fn.vforum_comments.instances = new Object();
		$.fn.vforum_comments_refresh = function(){
		};

		// default options
		$.fn.vforum_comments.defaults = {
			'userId' : false,
			'answerButtonClass' : 'js-forum-answer',
			'complaintButtonClass' : 'js-forum-complaint',
			'commentFormClass' : 'comment-form',
            'commentsContainerClass' : 'comments-container',
            'itemClass' : 'message',
            'itemAnchorPrefix' : 'comment',
            'onAuthorize' : false
		};

		var VForumComments = function(obj, o, instance_id){

			var container = $(obj);
            var commentsContainer = container.find("."+o.commentsContainerClass);
            var commentFormContainer = container.find("."+o.commentFormClass);
            var commentForm = commentFormContainer.find("form");
            var isAuthorized = o.userId ? true : false;
            var commentTextarea = commentForm.find("textarea");
            var commentParentIdInput = commentForm.find("input[type=hidden]");

            var resetForm = function () {
                commentTextarea.val("");
                commentParentIdInput.val("");
            }

            var answer = function (el) {
                if (!isAuthorized) {
                    showAuthMessage();
                    return false;
                }
                resetForm();
                var parentId = $(el).closest("."+o.itemClass).attr("itemId");
                commentParentIdInput.attr("value", parentId);

                var formUrl = commentForm.attr("action");
                formUrl += (formUrl.indexOf('?') >= 0 ? '&' : '?') + 'replyTo='+parentId;
                $.ajax({
                    url: formUrl,
                    data: {},
                    type: 'post',
                    dataType: 'json',
                    success: function(result) {
                        if (result.success)
                        {
                            commentTextarea.val(result.text);
                            commentForm.show();
                            commentTextarea.focus();
                        }
                        else
                            alert ("Ошибка отправки комментария. Попробуйте позже");
                        }
                    });
                    return false;
            };

            var showAuthMessage = function () {
                if (o.onAuthorize)
                    o.onAuthorize();
                else
                    alert ("Чтобы оставить комментарий вы должны войти на сайт");
                return false;
            }

            var sendComment = function () {
                var formData = commentForm.serialize();
                var formUrl = commentForm.attr("action");
                $.ajax({
                    url: formUrl,
                    data: formData,
                    type: 'post',
                    dataType: 'json',
                    success: function(result) {
                        if (result.success)
                        {
                            commentsContainer.append(result.comment);
                            commentForm.hide();
                            window.location.hash = o.itemAnchorPrefix+result.id;
                        }
                        else
                            if (result.error)
                            {
                                for (i in result.errors)
                                {
                                    
                                }
                            }
                            else
                            {
                                alert ("Ошибка отправки комментария. Попробуйте позже");
                            }
                        }
                    });
                    return false;
            };

            container.delegate("."+o.answerButtonClass, 'click', function(){
                answer(this);
                return false;
            });

            commentForm.delegate("input[type=submit]", 'click', function(){
                sendComment();
                return false;
            });

		};

	})(jQuery);