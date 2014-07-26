var tp_news_row = '<div class="news_wrapper_div" style="border-bottom:solid 1px #cccccc;background:{b_c}">' +
                        '<div class="news_title_wrapper_div">' +
                            '<img src="{n_a}" class="news_agency_img" style="float: right;" />' + 
                            '<a href="#" class="news_details_link{n_u}" data-href="news_details.html" data-id="{n_i}" data-persist-ajax="true" data-refresh-ajax="true">' +
                                '<span class="news_title_span aright">{n_t}</span>' +
                            '</a>' +
                        '</div>{n_p}'+
                  '</div>';
                
var tp_news_image = '<a href="#" class="news_details_link{n_u}" data-href="news_details.html" data-id="{n_i}" data-persist-ajax="true" data-refresh-ajax="true">'+
                        '<div class="news_image_div">'+
                            '<img class="news_img" src="{g_w}uploads/news/pics/{n_p}" />' +
                        '</div>'+
                    '</a>';
            
var tp_news_more = '<div class="more_news_div_{n_c}"></div>' + 
                        '<a href="#" class="news_more_link" style="text-decoration:none;line-height:35px;" data-urgent="{n_u}" data-date="{l_d}" data-cat="{n_c}" data-id="{n_i}">'+
                            '<div class="news_category_footer">{m_l}</div>'+
                        '</a>';