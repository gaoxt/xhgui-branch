<?php

class Xhgui_Controller_Rank extends Xhgui_Controller
{

    protected $_app;
    protected $_profiles;

    public function __construct($app, $profiles)
    {
        $this->_app = $app;
        $this->_profiles = $profiles;
    }

    public function index()
    {
        $request = $this->_app->request();
        $search = array();
        $keys = array("remote_addr", 'request_start', 'request_end');
        foreach ($keys as $key) {
            if ($request->get($key)) {
                $search[$key] = trim($request->get($key));
            }
        }

        if(isset($search['request_start']) && strpos($search['request_start'],'-')!==false)
        {
            $search['request_start'] = strtotime($search['request_start']);
        }

        if(isset($search['request_end']) && strpos($search['request_end'],'-')!==false)
        {
            $search['request_end'] = strtotime($search['request_end']);
        }

        $result = $this->_profiles->getAll(array(
            'sort' => 'time',
            'direction' => 'asc',
            'conditions' => $search,
            'projection' => true
        ));

        $paging = array(
            'total_pages' => $result['totalPages'],
            'page' => $result['page'],
            'sort' => 'asc',
            'direction' => $result['direction']
        );

        $this->_template = 'rank/list.twig';


        if(isset($search['request_start'])&& strpos($search['request_start'],'-')===false)
        {
            $search['request_start'] = date('Y-m-d H:i:s',$search['request_start']);
        }
        if(isset($search['request_end'])&& strpos($search['request_end'],'-')===false)
        {
            $search['request_end'] = date('Y-m-d H:i:s',$search['request_end']);
        }
        $this->set(array(
            'runs' => $result['results'],
            'search' => $search,
            'paging' => $paging,
            'base_url' => 'rank.list',
            'title' => '排行榜',
            'date_format' => $this->_app->config('date.format')
        ));
    }
}
