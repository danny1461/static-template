(function() {

    function start() {
        var $ = window.jQuery;

        $(document).on('click', '.end--mark', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();

            onEdit(this);
        });

        $(document).on('dblclick', function(e) {
            var endMark = isDynamicContent(e.target);
            if (endMark) {
                e.preventDefault();

                if (typeof window.getSelection == 'function') {
                    var sel = window.getSelection();
                    sel.removeAllRanges();
                }

                onEdit(endMark);
            }
        });

        $(document).on('keydown', function(e) {
            if (e.keyCode == 122 && e.ctrlKey) {
                updateSrClass(true);
            }
        });

        updateSrClass();
    }

    function updateSrClass(makeUpdate) {
        var classAdded = false;
        try {
            classAdded = JSON.parse(sessionStorage.getItem('dev-sr-only'));
        }
        catch (err) {}

        if (makeUpdate) {
            classAdded = !classAdded;
            sessionStorage.setItem('dev-sr-only', JSON.stringify(classAdded));
        }

        $('body').toggleClass('view-sr-only', classAdded);
    }

    function isDynamicContent(el) {
        var sibling = el;
        while (sibling = sibling.nextSibling) {
            if (sibling.className == 'begin--mark') {
                return null;
            }

            if (sibling.className == 'end--mark') {
                return sibling;
            }
        }

        if (el.parentNode) {
            return isDynamicContent(el.parentNode);
        }
    }

    function onEdit(that) {
        var $this = $(that);
            analysis = analyzeContent(that),
            newContent = prompt('New content:', analysis.getCurrentContent());

        if (newContent === null) {
            return;
        }

        newContent = newContent.trim();
        if (!newContent) {
            newContent = '&nbsp;';
        }

        $.ajax({
            url: location.href,
            dataType: 'html',
            method: 'post',
            data: {
                type: 'devMode',
                file: $this.data('file'),
                varName: $this.data('var'),
                newContent: newContent
            },

            complete: function(_jqXHR, status) {
                if (status != 'success') {
                    alert('Unable to update field');
                    return;
                }

                // Insert new content
                analysis.replaceNodes(newContent);
            }
        });
    }

    function analyzeContent(editNode) {
        var node = editNode,
            currentNodes = [];

        while ((node = node.previousSibling) && node.className != 'begin--mark') {
            currentNodes.unshift(node);
        }

        var isSimpleText = currentNodes.length == 1 && currentNodes[0].className == 'page-data--wrapper';

        return {
            currentNodes: currentNodes,

            getCurrentContent: function() {
                var nodes = currentNodes;
                if (isSimpleText) {
                    nodes = nodes[0].childNodes;
                }

                var content = '';
                for (var i = 0; i < nodes.length; i++) {
                    content += nodes[i].outerHTML || nodes[i].textContent;
                }

                return content;
            },

            replaceNodes: function(newContent) {
                var i, div;

                for (i = 0; i < currentNodes.length; i++) {
                    editNode.parentNode.removeChild(currentNodes[i]);
                }

                if (newContent[newContent.length - 1] != '>') {
                    newContent = '<span class="page-data--wrapper">' + newContent + '</span>';
                }

                // Insert new content
                div = document.createElement('div');
                div.innerHTML = newContent;
                while (div.childNodes.length) {
                    editNode.parentNode.insertBefore(div.childNodes[0], editNode);
                }
            }
        };
    }

    function docReady(fn) {
        if (document.readyState != 'loading'){
            fn();
        }
        else {
            document.addEventListener('DOMContentLoaded', fn);
        }
    }

    docReady(function() {
        if (window.jQuery) {
            start();
        }
        else {
            var script = document.createElement('script'),
                loaded = false;
            document.head.appendChild(script);
            script.onload = script.onreadystatechange = function() {
                if (!loaded && (!this.readyState || this.readyState == 'loaded' || this.readyState == 'complete')) {
                    loaded = true;
                    script.onload = script.onreadystatechange = null;
                    start();
                }
            };
            script.src = 'https://code.jquery.com/jquery-3.4.1.min.js';
        }
    });

})();