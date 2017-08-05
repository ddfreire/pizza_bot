<?php

    class State
    {

        public static function getState()
        {
            $db = PizzaBot::getDb();
            $contextId = PizzaBot::getContextId();

            $rs = $db->prepare('select * from tb_state where state_id = ?');
            $rs->bindParam(1, $contextId, PDO::PARAM_INT);
            $rs->execute();
            $result = $rs->fetch();

            if(empty($result)) {
                self::insert($contextId);
                return self::getState();
            }

            return $result;
        }

        private static function insert($contextId)
        {
            $now = date('Y-m-d H:i:s');
            $db = PizzaBot::getDb();
            $rs = $db->prepare('INSERT INTO tb_state (state_id, state_created_at) values(?, ?)');
            $rs->bindParam(1, $contextId, PDO::PARAM_INT);
            $rs->bindParam(2, $now);
            return $rs->execute();
        }

        public static function save($contextId, $state)
        {
            $now = date('Y-m-d H:i:s');
            $db = PizzaBot::getDb();

            $query = 'update tb_state set ';

            $params = [];
            foreach($state as $key => $value) {
                $query .= 'state_' . $key . ' = ?' ;
                $params[] = $value;
            }

            $query .= ' where state_id = ?';

            $rs = $db->prepare($query);

            foreach ($params as $key => $param) {
                $rs->bindParam(($key + 1), $param);
            }

            $rs->bindParam((count($params) + 1), $contextId);

            return $rs->execute();
        }

    }