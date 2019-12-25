<?php

namespace Phplight\Database;

use Phplight\File\File;
use PDO;
use PDOException;
use Exception;
use Phplight\Http\Request;
use Phplight\Url\Url;


class Database {

    protected static $instance;
    protected static $connection;
    protected static $select;
    protected static $table;
    protected static $join;
    protected static $where;
    protected static $where_binding = [];
    protected static $having;
    protected static $having_binding = [];
    protected static $groupBy;
    protected static $groupBy_binding = [];
    protected static $orderBy;
    protected static $limit;
    protected static $offset;
    protected static $query;
    protected static $binding = [];
    protected static $setter;

    /**
     * Database constructor.
     *
     * @return void
     */
    private function __construct() {}

    /**
     * connect to database.
     *
     * @throws Exception
     */
    private static function connect() {
        if (! static::$connection) {
            $database_data = File::require_file('config/database.php');
            extract($database_data);
            $dsn = "mysql:host=$host;dbname=$database";
            $option = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_PERSISTENT => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "set NAMES " . $charset . " COLLATE " . $collection,
            ];
            try{
                static::$connection = new PDO($dsn, $username, $password, $option);
            } catch (PDOException $e) {
                throw new Exception($e->getMessage());
            }
        }
    }

    /**
     * create instance.
     *
     * @return mixed
     * @throws Exception
     */
    private static function instance() {
        if (! static::$instance) {
            static::connect();
            static::$instance = new Database();
        }

        return static::$instance;
    }

    /**
     * query data.
     *
     * @param null $query
     * @return mixed
     * @throws Exception
     */
    public static function query($query = null) {
        static::instance();
        if ($query == null) {
            if (! static::$table) {
                throw new Exception("unknown table");
            }
            $query = "SELECT ";
            $query .= static::$select ?: '*';
            $query .= " FROM " . static::$table. " ";
            $query .= static::$join;
            $query .= static::$where;
            $query .= static::$groupBy;
            $query .= static::$having;
            $query .= static::$orderBy;
            $query .= static::$limit;
            $query .= static::$offset;
        }

        static::$query = $query;
        static::$binding = array_merge(static::$where_binding, static::$having_binding);
        return static::instance();
    }

    /**
     * select data from table.
     *
     * @return mixed
     * @throws Exception
     */
    public static function select() {
        $select = func_get_args();
        $select = implode(',', $select);

        static::$select = $select;
        return static::instance();
    }


    /**
     * define table.
     *
     * @param $table
     * @return mixed
     * @throws Exception
     */
    public static function table($table) {
        static::$table = $table;
        return static::instance();
    }

    /**
     * join two table.
     *
     * @param $table
     * @param $first
     * @param $operator
     * @param $second
     * @param string $type
     * @return mixed
     * @throws Exception
     */
    public static function join($table, $first, $operator, $second, $type = "INNER") {
        static::$join .= " " . $type . " JOIN " . $table . " ON " . $first . $operator . $second. " ";
        return static::instance();
    }

    /**
     * right join two tables.
     *
     * @param $table
     * @param $first
     * @param $operator
     * @param $second
     * @return mixed
     * @throws Exception
     */
    public static function rightJoin($table, $first, $operator, $second) {
        static::join($table, $first, $operator, $second, "RIGHT");
        return static::instance();
    }

    /**
     * left join two tables.
     *
     * @param $table
     * @param $first
     * @param $operator
     * @param $second
     * @return mixed
     * @throws Exception
     */
    public static function leftJoin($table, $first, $operator, $second) {
        static::join($table, $first, $operator, $second, "LEFT");
        return static::instance();
    }

    /**
     * where query.
     *
     * @param $column
     * @param $operator
     * @param $value
     * @param null $type
     * @return mixed
     * @throws Exception
     */
    public static function where($column, $operator, $value, $type = null) {
        $where = '`' . $column . '`' . $operator . ' ? ';
        if (! static::$where ) {
            $statement = " WHERE " . $where;
        } else {
            if ($type == null) {
                $statement = " AND " . $where;
            } else {
                $statement = " " . $type . " " . $where;
            }
        }
        static::$where .= $statement;
        static::$where_binding[] = htmlspecialchars($value);
        return static::instance();
    }

    /**
     * or where query.
     *
     * @param $column
     * @param $operator
     * @param $value
     * @return mixed
     * @throws Exception
     */
    public static function orWhere($column, $operator, $value) {
        static::where($column, $operator, $value, 'OR');
        return static::instance();
    }

    /**
     * group by query.
     *
     * @return mixed
     * @throws Exception
     */
    public static function groupBy() {
        $group_by = func_get_args();
        $group_by = " GROUP BY " . implode(', ', $group_by) . " ";
        static::$groupBy = $group_by;

        return static::instance();
    }

    /**
     * having query.
     *
     * @param $column
     * @param $operator
     * @param $value
     * @return mixed
     * @throws Exception
     */
    public static function having($column, $operator, $value) {
        $having = '`' . $column . '`' . $operator . ' ? ';
        if (! static::$having ) {
            $statement = " WHERE " . $having;
        } else {
                $statement = " AND " . $having;
        }
        static::$having .= $statement;
        static::$having_binding[] = htmlspecialchars($value);
        return static::instance();
    }

    /**
     * order by query.
     *
     * @param $column
     * @param string $type
     * @return mixed
     * @throws Exception
     */
    public static function orderBy($column, $type = 'desc') {
        $sep = static::$orderBy ? " , " : " ORDER BY ";
        $type = strtoupper($type);
        $type = ($type != null && in_array($type, ['ASC', 'DESC'])) ? $type : 'ASC';

        $statment = $sep . $column . " " .  $type. " ";
        static::$orderBy .= $statment;

        return static::instance();
    }

    /**
     * limit query.
     *
     * @param $limit
     * @return mixed
     * @throws Exception
     */
    public static function limit($limit) {
        static::$limit = " LIMIT " . $limit . " ";

        return static::instance();
    }

    /**
     * offset query.
     *
     * @param $offset
     * @return mixed
     * @throws Exception
     */
    public static function offset($offset) {
        static::$offset = " OFFSET " . $offset . " ";

        return static::instance();
    }

    /**
     * fetch and execute data.
     *
     * @return mixed
     * @throws Exception
     */
    private static function fetchExecute() {
        static::query(static::$query);
        $query = trim(static::$query, ' ');
        $data = static::$connection->prepare($query);
        $data->execute(static::$binding);

        static::clear();

        return $data;
    }

    /**
     * execute data.
     *
     * @param array $data
     * @param $query
     * @param bool $where
     * @throws Exception
     */
    public static function execute(array $data, $query, $where = null) {
        static::instance();
        if ( ! static::$table) {
            throw new Exception("unknown table");
        }

        foreach ($data as $key => $value) {
            static::$setter  .= '`' . $key . '` = ?, ';
            static::$binding[] = filter_var($value, FILTER_SANITIZE_STRING);
        }

        static::$setter = trim(static::$setter, ', ');

        $query .= static::$setter;
        $query .= $where != null ? static::$where . " " : '';
        static::$binding = $where != null ? array_merge(static::$binding, static::$where_binding) : static::$binding;

        $data = static::$connection->prepare($query);
        $data->execute(static::$binding);

        static::clear();
    }

    /**
     * insert in database.
     *
     * @param array $data
     * @return mixed
     * @throws Exception
     */
    public static function insert(array $data) {
        $table = static::$table;
        $query = " INSERT INTO " . $table . " SET ";
        static::execute($data, $query);

        $object_id = static::$connection->lastInsertId();
        $object = static::table($table)->where('id', '=', $object_id)->first();

        return $object;
    }

    /**
     * update specific record.
     *
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public static function update(array $data) {
        $query = "UPDATE " . static::$table . " SET ";
        static::execute($data, $query, true);

        return true;
    }

    /**
     * delete specific record.
     *
     * @return bool
     * @throws Exception
     */
    public static function delete() {
        $query = "DELETE FROM " . static::$table;
        static::execute([], $query, true);

        return true;
    }

    /**
     * paginate data.
     *
     * @param int $per_page
     * @return array
     * @throws Exception
     */
    public static function paginate($per_page = 15) {
        static::query(static::$query);
        $query = trim(static::$query, ' ');
        $data = static::$connection->prepare($query);
        $data->execute();
        $pages = ceil($data->rowCount() / $per_page);

        $page = Request::get('page');
        $current_page = (! is_numeric($page) || Request::get('page') < 1) ? "1" : $page;
        $offset = ($current_page - 1) * $per_page;
        static::limit($per_page);
        static::offset($offset);
        static::query();

        $data = static::fetchExecute();
        $result = $data->fetchAll();

        $response = ['data' => $result, 'per_page' => $per_page, 'pages' => $pages, 'current_page' => $current_page];
        return $response;
    }

    /**
     * links for pagination
     *
     * @param $current_page
     * @param $pages
     * @return string
     */
    public static function links($current_page, $pages) {
        $links = '';
        $from = $current_page - 2;
        $to = $current_page + 2;
        if ($from < 2) {
            $from = 2;
            $to = $from + 4;
        }
        if ($to >= $pages) {
            $diff = $to - $pages + 1;
            $from = ($from > 2) ? $from - $diff : 2;
            $to = $pages - 1;
        }
        if ($from < 2) {$from = 1;}
        if ($to >= $pages) {$to = ($pages - 1);}
        if ($pages > 1) {
            $links .= "<ul class='pagination'>";
            $full_link = Url::path(Request::getFullUrl());
            $full_link = preg_replace('/\?page=(.*)/', '', $full_link);
            $full_link = preg_replace('/\&page=(.*)/', '', $full_link);
            $current_page_active = $current_page == 1 ? 'active' : '';
            $href = strpos($full_link, '?') ? ($full_link.'&page=1') : ($full_link.'?page=1');
            $links .= "<li class='link' $current_page_active><a href='$href'>First</a></li>";
            for($i = $from; $i<= $to; $i++) {
                $current_page_active = $current_page == $i ? 'active' : '';
                $href = strpos($full_link, '?') ? ($full_link.'&page='.$i) : ($full_link.'?page='.$i);
                $links .= "<li class='link' $current_page_active><a href='$href'>$i</a></li>";
            }
            if ($pages > 1) {
                $current_page_active = $current_page == $pages ? 'active' : '';
                $href = strpos($full_link, '?') ? ($full_link.'&page='.$pages) : ($full_link.'?page='.$pages);
                $links .= "<li class='link' $current_page_active><a href='$href'>Last</a></li>";
            }
            return $links;
        }
    }


    /**
     * get all data
     *
     * @return mixed
     * @throws Exception
     */
    public static function get() {
        $data = static::fetchExecute();
        $result = $data->fetchAll();

        return $result;
    }


    /**
     * get first record.
     *
     * @return mixed
     * @throws Exception
     */
    public static function first() {
        $data = static::fetchExecute();
        $result = $data->fetch();

        return $result;
    }

    /**
     * clear all.
     */
    private static function clear() {
        static::$select = '';
        static::$join = '';
        static::$where = '';
        static::$where_binding = [];
        static::$groupBy = '';
        static::$groupBy_binding = [];
        static::$having = '';
        static::$having_binding = [];
        static::$binding = [];
        static::$query = '';
        static::$offset = '';
        static::$limit = '';
        static::$instance = '';
    }

    /**
     * get query.
     *
     * @return mixed
     * @throws Exception
     */
    public static function getQuery() {
        static::query(static::$query);
        return static::$query;
    }
}