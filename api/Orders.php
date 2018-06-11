<?php

/**
 * Simpla CMS
 *
 * @copyright     2014 Denis Pikusov
 * @link          http://simplacms.ru
 * @author        Denis Pikusov
 *
 */

require_once('Simpla.php');

class Orders extends Simpla
{

    public function get_order($id)
    {
        if (is_int($id)) {
            $where = $this->db->placehold(' WHERE o.id=? ', intval($id));
        } else {
            $where = $this->db->placehold(' WHERE o.url=? ', $id);
        }

        $query = $this->db->placehold("SELECT  o.id, o.delivery_id, o.delivery_price, o.separate_delivery,
										o.payment_method_id, o.paid, o.payment_date, o.closed, o.discount, o.coupon_code, 
										o.coupon_discount,
										o.date, o.user_id, o.name, o.address, o.phone, o.email, o.comment, o.status,
										o.url, o.total_price, o.note, o.ip,
										/* ukrposhta */
                                        u.recipient_postcode, u.recipient_name, u.recipient_sername,
                                        u.parcel_weight, u.post_pay, u.paid_by, u.shipment_file_name,
                                        u.post_office_address, u.delivery_price
                                        /* /ukrposhta */ 
										FROM __orders o 
										/* ukrposhta */
										LEFT JOIN __ukrposhta_order u ON o.id = u.order_id
										/* /ukrposhta */
										$where LIMIT 1");

        if ($this->db->query($query)) {
            return $this->db->result();
        } else {
            return false;
        }
    }

    function get_orders($filter = array())
    {
        // По умолчанию
        $limit                 = 100;
        $page                  = 1;
        $keyword_filter        = '';
        $label_filter          = '';
        $status_filter         = '';
        $user_filter           = '';
        $modified_since_filter = '';
        $id_filter             = '';

        if (isset($filter['limit'])) {
            $limit = max(1, intval($filter['limit']));
        }

        if (isset($filter['page'])) {
            $page = max(1, intval($filter['page']));
        }

        $sql_limit = $this->db->placehold(' LIMIT ?, ? ', ($page - 1) * $limit, $limit);


        if (isset($filter['status'])) {
            $status_filter = $this->db->placehold('AND o.status = ?', intval($filter['status']));
        }

        if (isset($filter['id'])) {
            $id_filter = $this->db->placehold('AND o.id in(?@)', (array)$filter['id']);
        }

        if (isset($filter['user_id'])) {
            $user_filter = $this->db->placehold('AND o.user_id = ?', intval($filter['user_id']));
        }

        if (isset($filter['modified_since'])) {
            $modified_since_filter = $this->db->placehold('AND o.modified > ?', $filter['modified_since']);
        }

        if (isset($filter['label'])) {
            $label_filter = $this->db->placehold('AND ol.label_id = ?', $filter['label']);
        }

        if (!empty($filter['keyword'])) {
            $keywords = explode(' ', $filter['keyword']);
            foreach ($keywords as $keyword) {
                $keyword_filter .= $this->db->placehold('AND (o.id = "' . $this->db->escape(trim($keyword)) . '" OR o.name LIKE "%' . $this->db->escape(trim($keyword)) . '%" OR REPLACE(o.phone, "-", "")  LIKE "%' . $this->db->escape(str_replace('-', '', trim($keyword))) . '%" OR o.address LIKE "%' . $this->db->escape(trim($keyword)) . '%" )');
            }
        }

        // Выбираем заказы
        $query = $this->db->placehold("SELECT o.id, o.delivery_id, o.delivery_price, o.separate_delivery,
										o.payment_method_id, o.paid, o.payment_date, o.closed, o.discount, o.coupon_code, o.coupon_discount,
										o.date, o.user_id, o.name, o.address, o.phone, o.email, o.comment, o.status,
										o.url, o.total_price, o.note
									FROM __orders AS o 
									LEFT JOIN __orders_labels AS ol ON o.id=ol.order_id 
									WHERE 1
									$id_filter $status_filter $user_filter $keyword_filter $label_filter $modified_since_filter GROUP BY o.id ORDER BY status, id DESC $sql_limit", "%Y-%m-%d");
        $this->db->query($query);
        $orders = array();
        foreach ($this->db->results() as $order) {
            $orders[$order->id] = $order;
        }
        return $orders;
    }

    function count_orders($filter = array())
    {
        $keyword_filter = '';
        $label_filter   = '';
        $status_filter  = '';
        $user_filter    = '';

        if (isset($filter['status'])) {
            $status_filter = $this->db->placehold('AND o.status = ?', intval($filter['status']));
        }

        if (isset($filter['user_id'])) {
            $user_filter = $this->db->placehold('AND o.user_id = ?', intval($filter['user_id']));
        }

        if (isset($filter['label'])) {
            $label_filter = $this->db->placehold('AND ol.label_id = ?', $filter['label']);
        }

        if (!empty($filter['keyword'])) {
            $keywords = explode(' ', $filter['keyword']);
            foreach ($keywords as $keyword) {
                $keyword_filter .= $this->db->placehold('AND (o.name LIKE "%' . $this->db->escape(trim($keyword)) . '%" OR REPLACE(o.phone, "-", "")  LIKE "%' . $this->db->escape(str_replace('-', '', trim($keyword))) . '%" OR o.address LIKE "%' . $this->db->escape(trim($keyword)) . '%" )');
            }
        }

        // Выбираем заказы
        $query = $this->db->placehold("SELECT COUNT(DISTINCT id) as count
									FROM __orders AS o 
									LEFT JOIN __orders_labels AS ol ON o.id=ol.order_id 
									WHERE 1
									$status_filter $user_filter $label_filter $keyword_filter");
        $this->db->query($query);
        return $this->db->result('count');
    }

    /**
     * @param int                 $id
     * @param stdClass|array      $order
     * @param null|stdClass|array $ukrposhta
     *
     * @return int
     */
    public function update_order($id, $order, $ukrposhta = null)
    {
        $query = $this->db->placehold("UPDATE __orders SET ?%, modified=now() WHERE id=? LIMIT 1", $order, intval($id));
        $this->db->query($query);
        $this->update_total_price(intval($id));

        /* ukrposhta */
        if ($order->delivery_id == 777 && !empty($ukrposhta)) {
            is_array($ukrposhta) ? $ukrposhta['order_id'] = $id : $ukrposhta->order_id = $id;
            $this->update_ukrposhta($id, $ukrposhta);
        }
        /* /ukrposhta */

        return $id;
    }

    public function delete_order($id)
    {
        if (!empty($id)) {
            $query = $this->db->placehold("DELETE FROM __purchases WHERE order_id=?", $id);
            $this->db->query($query);

            $query = $this->db->placehold("DELETE FROM __orders_labels WHERE order_id=?", $id);
            $this->db->query($query);

            /* ukrposhta*/
            $this->delete_ukrposhta_shipment_file(intval($id));
            $query = $this->db->placehold("DELETE FROM __ukrposhta_order WHERE order_id=?", $id);
            $this->db->query($query);
            /* /ukrposhta*/

            $query = $this->db->placehold("DELETE FROM __orders WHERE id=? LIMIT 1", $id);
            $this->db->query($query);
        }
    }

    /**
     * @param stdClass|array      $order
     * @param null|stdClass|array $ukrposhta
     *
     * @return int
     */
    public function add_order($order, $ukrposhta = null)
    {
        $order         = (object)$order;
        $order->url    = md5(uniqid($this->config->salt, true));
        $set_curr_date = '';
        if (empty($order->date)) {
            $set_curr_date = ', date=now()';
        }
        $query = $this->db->placehold("INSERT INTO __orders SET ?%$set_curr_date", $order);
        $this->db->query($query);
        $id = $this->db->insert_id();

        /* ukrposhta */
        if ($order->delivery_id == 777 && !empty($ukrposhta)) {
            is_array($ukrposhta) ? $ukrposhta['order_id'] = $id : $ukrposhta->order_id = $id;
            $this->add_ukrposhta($ukrposhta);
        }
        /* /ukrposhta */

        return $id;
    }

    /* ukrposhta */

    /**
     * @param int $order_id
     *
     * @return stdClass|null
     */
    public function get_order_ukrposhta($order_id)
    {
        if (empty($order_id)) {
            return null;
        }

        $where = $this->db->placehold('WHERE o.id = ? AND u.id IS NOT NULL', $order_id);


        $query = $this->db->placehold("SELECT 
                                          u.id, u.order_id,
                                          u.recipient_postcode, 
                                          u.recipient_name, u.recipient_sername,
                                          u.parcel_weight, u.post_pay, u.paid_by, u.shipment_file_name,
                                          u.post_office_address, u.delivery_price
					                   FROM __orders o LEFT JOIN __ukrposhta_order u ON o.id = u.order_id
					                   $where
					                   LIMIT 1
					                   ");

        $this->db->query($query);
        return $this->db->result();
    }

    /**
     * @param int $order_id
     *
     * @return int ukrposhta id
     */
    public function get_order_ukrposhta_id($order_id)
    {
        $ukrposhta = $this->get_order_ukrposhta($order_id);
        return (int)$ukrposhta->id;
    }

    /**
     * Update ukrposhta_order table.
     *
     * @param                $order_id
     * @param stdClass|array $ukrposhta
     *
     * @return void
     */
    public function update_ukrposhta($order_id, $ukrposhta)
    {
        // Если такой записи нет, то создать запись:
        if (empty($this->get_order_ukrposhta($order_id))) {
            $this->add_ukrposhta($ukrposhta);
        } else { // Обновить запись:
            $query = $this->db->placehold("UPDATE __ukrposhta_order SET ?% WHERE order_id = ?", $ukrposhta, $order_id);
            $this->db->query($query);
        }
    }

    /**
     * Remove a generated shipment file both locally and form the database.
     * Return false if it was impossible to remove a file form the file system.
     * Also additional information about the shipment will be removed.
     *
     * @param int $order_id
     *
     * @return bool
     */
    public function delete_ukrposhta_shipment_file($order_id)
    {
        $ukrposhta = $this->get_order_ukrposhta($order_id);
        $full_path = $this->config->root_dir . "files/ukrpost/$ukrposhta->shipment_file_name";
        $result    = true;

        if (file_exists($full_path)) {
            unlink($full_path);
        } else {
            $result = false;
        }

        $ukrposhta->shipment_file_name  = null;
        $ukrposhta->post_office_address = null;
        $ukrposhta->delivery_price      = null;
        $this->update_ukrposhta($order_id, $ukrposhta);

        return $result;
    }

    /**
     * Add a new ukrposhta entity to the database.
     *
     * @param stdClass|array $ukrposhta
     *
     * @return int ukrposhta id
     */
    public function add_ukrposhta($ukrposhta)
    {
        $query = $this->db->placehold("INSERT INTO __ukrposhta_order SET ?%", $ukrposhta);
        $this->db->query($query);
        return $this->db->insert_id();
    }


    /* /ukrposhta */

    public function get_label($id)
    {
        $query = $this->db->placehold("SELECT * FROM __labels WHERE id=? LIMIT 1", intval($id));
        $this->db->query($query);
        return $this->db->result();
    }

    public function get_labels()
    {
        $query = $this->db->placehold("SELECT * FROM __labels ORDER BY position");
        $this->db->query($query);
        return $this->db->results();
    }

    /*
    *
    * Создание метки заказов
    * @param $label
    *
    */
    public function add_label($label)
    {
        $query = $this->db->placehold('INSERT INTO __labels SET ?%', $label);
        if (!$this->db->query($query)) {
            return false;
        }

        $id = $this->db->insert_id();
        $this->db->query("UPDATE __labels SET position=id WHERE id=?", $id);
        return $id;
    }


    /*
    *
    * Обновить метку
    * @param $id, $label
    *
    */
    public function update_label($id, $label)
    {
        $query = $this->db->placehold("UPDATE __labels SET ?% WHERE id in(?@) LIMIT ?", $label, (array)$id, count((array)$id));
        $this->db->query($query);
        return $id;
    }

    /*
    *
    * Удалить метку
    * @param $id
    *
    */
    public function delete_label($id)
    {
        if (!empty($id)) {
            $query = $this->db->placehold("DELETE FROM __orders_labels WHERE label_id=?", intval($id));
            if ($this->db->query($query)) {
                $query = $this->db->placehold("DELETE FROM __labels WHERE id=? LIMIT 1", intval($id));
                return $this->db->query($query);
            } else {
                return false;
            }
        }
    }

    function get_order_labels($order_id = array())
    {
        if (empty($order_id)) {
            return array();
        }

        $label_id_filter = $this->db->placehold('AND order_id in(?@)', (array)$order_id);

        $query = $this->db->placehold("SELECT ol.order_id, l.id, l.name, l.color, l.position
					FROM __labels l LEFT JOIN __orders_labels ol ON ol.label_id = l.id
					WHERE 
					1
					$label_id_filter   
					ORDER BY position       
					");

        $this->db->query($query);
        return $this->db->results();
    }

    public function update_order_labels($id, $labels_ids)
    {
        $labels_ids = (array)$labels_ids;
        $query      = $this->db->placehold("DELETE FROM __orders_labels WHERE order_id=?", intval($id));
        $this->db->query($query);
        if (is_array($labels_ids)) {
            foreach ($labels_ids as $l_id) {
                $this->db->query("INSERT INTO __orders_labels SET order_id=?, label_id=?", $id, $l_id);
            }
        }
    }

    public function add_order_labels($id, $labels_ids)
    {
        $labels_ids = (array)$labels_ids;
        if (is_array($labels_ids)) {
            foreach ($labels_ids as $l_id) {
                $this->db->query("INSERT IGNORE INTO __orders_labels SET order_id=?, label_id=?", $id, $l_id);
            }
        }
    }

    public function delete_order_labels($id, $labels_ids)
    {
        $labels_ids = (array)$labels_ids;
        if (is_array($labels_ids)) {
            foreach ($labels_ids as $l_id) {
                $this->db->query("DELETE FROM __orders_labels WHERE order_id=? AND label_id=?", $id, $l_id);
            }
        }
    }


    public function get_purchase($id)
    {
        $query = $this->db->placehold("SELECT * FROM __purchases WHERE id=? LIMIT 1", intval($id));
        $this->db->query($query);
        return $this->db->result();
    }

    public function get_purchases($filter = array())
    {
        $order_id_filter = '';
        if (!empty($filter['order_id'])) {
            $order_id_filter = $this->db->placehold('AND order_id in(?@)', (array)$filter['order_id']);
        }

        $query = $this->db->placehold("SELECT * FROM __purchases WHERE 1 $order_id_filter ORDER BY id");
        $this->db->query($query);
        return $this->db->results();
    }

    public function update_purchase($id, $purchase)
    {
        $purchase     = (object)$purchase;
        $old_purchase = $this->get_purchase($id);
        if (!$old_purchase) {
            return false;
        }

        $order = $this->get_order(intval($old_purchase->order_id));
        if (!$order) {
            return false;
        }

        // Не допустить нехватки на складе
        $variant = $this->variants->get_variant($purchase->variant_id);
        if ($order->closed && !empty($purchase->amount) && !empty($variant) && !$variant->infinity && $variant->stock < ($purchase->amount - $old_purchase->amount)) {
            return false;
        }

        // Если заказ закрыт, нужно обновить склад при изменении покупки
        if ($order->closed && !empty($purchase->amount)) {
            if ($old_purchase->variant_id != $purchase->variant_id) {
                if (!empty($old_purchase->variant_id)) {
                    $query = $this->db->placehold("UPDATE __variants SET stock=stock+? WHERE id=? AND stock IS NOT NULL LIMIT 1", $old_purchase->amount, $old_purchase->variant_id);
                    $this->db->query($query);
                }
                if (!empty($purchase->variant_id)) {
                    $query = $this->db->placehold("UPDATE __variants SET stock=stock-? WHERE id=? AND stock IS NOT NULL LIMIT 1", $purchase->amount, $purchase->variant_id);
                    $this->db->query($query);
                }
            } elseif (!empty($purchase->variant_id)) {
                $query = $this->db->placehold("UPDATE __variants SET stock=stock+(?) WHERE id=? AND stock IS NOT NULL LIMIT 1", $old_purchase->amount - $purchase->amount, $purchase->variant_id);
                $this->db->query($query);
            }
        }

        $query = $this->db->placehold("UPDATE __purchases SET ?% WHERE id=? LIMIT 1", $purchase, intval($id));
        $this->db->query($query);
        $this->update_total_price($order->id);
        return $id;
    }

    public function add_purchase($purchase)
    {
        $purchase = (object)$purchase;
        if (!empty($purchase->variant_id)) {
            $variant = $this->variants->get_variant($purchase->variant_id);
            if (empty($variant)) {
                return false;
            }
            $product = $this->products->get_product(intval($variant->product_id));
            if (empty($product)) {
                return false;
            }
        }

        $order = $this->get_order(intval($purchase->order_id));
        if (empty($order)) {
            return false;
        }

        // Не допустить нехватки на складе
        if ($order->closed && !empty($purchase->amount) && !$variant->infinity && $variant->stock < $purchase->amount) {
            return false;
        }

        if (!isset($purchase->product_id) && isset($variant)) {
            $purchase->product_id = $variant->product_id;
        }

        if (!isset($purchase->product_name) && !empty($product)) {
            $purchase->product_name = $product->name;
        }

        if (!isset($purchase->sku) && !empty($variant)) {
            $purchase->sku = $variant->sku;
        }

        if (!isset($purchase->variant_name) && !empty($variant)) {
            $purchase->variant_name = $variant->name;
        }

        if (!isset($purchase->price) && !empty($variant)) {
            $purchase->price = $variant->price;
        }

        if (!isset($purchase->amount)) {
            $purchase->amount = 1;
        }

        // Если заказ закрыт, нужно обновить склад при добавлении покупки
        if ($order->closed && !empty($purchase->amount) && !empty($variant->id)) {
            $stock_diff = $purchase->amount;
            $query      = $this->db->placehold("UPDATE __variants SET stock=stock-? WHERE id=? AND stock IS NOT NULL LIMIT 1", $stock_diff, $variant->id);
            $this->db->query($query);
        }

        $query = $this->db->placehold("INSERT INTO __purchases SET ?%", $purchase);
        $this->db->query($query);
        $purchase_id = $this->db->insert_id();

        $this->update_total_price($order->id);
        return $purchase_id;
    }

    public function delete_purchase($id)
    {
        $purchase = $this->get_purchase($id);
        if (!$purchase) {
            return false;
        }

        $order = $this->get_order(intval($purchase->order_id));
        if (!$order) {
            return false;
        }

        // Если заказ закрыт, нужно обновить склад при изменении покупки
        if ($order->closed && !empty($purchase->amount)) {
            $stock_diff = $purchase->amount;
            $query      = $this->db->placehold("UPDATE __variants SET stock=stock+? WHERE id=? AND stock IS NOT NULL LIMIT 1", $stock_diff, $purchase->variant_id);
            $this->db->query($query);
        }

        $query = $this->db->placehold("DELETE FROM __purchases WHERE id=? LIMIT 1", intval($id));
        $this->db->query($query);
        $this->update_total_price($order->id);
        return true;
    }


    public function close($order_id)
    {
        $order = $this->get_order(intval($order_id));
        if (empty($order)) {
            return false;
        }

        if (!$order->closed) {
            $variants_amounts = array();
            $purchases        = $this->get_purchases(array('order_id' => $order->id));
            foreach ($purchases as $purchase) {
                if (isset($variants_amounts[$purchase->variant_id])) {
                    $variants_amounts[$purchase->variant_id] += $purchase->amount;
                } else {
                    $variants_amounts[$purchase->variant_id] = $purchase->amount;
                }
            }

            foreach ($variants_amounts as $id => $amount) {
                $variant = $this->variants->get_variant($id);
                if (empty($variant) || ($variant->stock < $amount)) {
                    return false;
                }
            }
            foreach ($purchases as $purchase) {
                $variant = $this->variants->get_variant($purchase->variant_id);
                if (!$variant->infinity) {
                    $new_stock = $variant->stock - $purchase->amount;
                    $this->variants->update_variant($variant->id, array('stock' => $new_stock));
                }
            }
            $query = $this->db->placehold("UPDATE __orders SET closed=1, modified=NOW() WHERE id=? LIMIT 1", $order->id);
            $this->db->query($query);
        }
        return $order->id;
    }

    public function open($order_id)
    {
        $order = $this->get_order(intval($order_id));
        if (empty($order)) {
            return false;
        }

        if ($order->closed) {
            $purchases = $this->get_purchases(array('order_id' => $order->id));
            foreach ($purchases as $purchase) {
                $variant = $this->variants->get_variant($purchase->variant_id);
                if ($variant && !$variant->infinity) {
                    $new_stock = $variant->stock + $purchase->amount;
                    $this->variants->update_variant($variant->id, array('stock' => $new_stock));
                }
            }
            $query = $this->db->placehold("UPDATE __orders SET closed=0, modified=NOW() WHERE id=? LIMIT 1", $order->id);
            $this->db->query($query);
        }
        return $order->id;
    }

    public function pay($order_id)
    {
        $order = $this->get_order(intval($order_id));
        if (empty($order)) {
            return false;
        }

        if (!$this->close($order->id)) {
            return false;
        }
        $query = $this->db->placehold("UPDATE __orders SET payment_status=1, payment_date=NOW(), modified=NOW() WHERE id=? LIMIT 1", $order->id);
        $this->db->query($query);
        return $order->id;
    }

    private function update_total_price($order_id)
    {
        $order = $this->get_order(intval($order_id));
        if (empty($order)) {
            return false;
        }

        $query = $this->db->placehold("UPDATE __orders o SET o.total_price=IFNULL((SELECT SUM(p.price*p.amount)*(100-o.discount)/100 FROM __purchases p WHERE p.order_id=o.id), 0)+o.delivery_price*(1-o.separate_delivery)-o.coupon_discount, modified=NOW() WHERE o.id=? LIMIT 1", $order->id);
        $this->db->query($query);
        return $order->id;
    }


    public function get_next_order($id, $status = null)
    {
        $f = '';
        if ($status !== null) {
            $f = $this->db->placehold('AND status=?', $status);
        }
        $this->db->query("SELECT MIN(id) as id FROM __orders WHERE id>? $f LIMIT 1", $id);
        $next_id = $this->db->result('id');
        if ($next_id) {
            return $this->get_order(intval($next_id));
        } else {
            return false;
        }
    }

    public function get_prev_order($id, $status = null)
    {
        $f = '';
        if ($status !== null) {
            $f = $this->db->placehold('AND status=?', $status);
        }
        $this->db->query("SELECT MAX(id) as id FROM __orders WHERE id<? $f LIMIT 1", $id);
        $prev_id = $this->db->result('id');
        if ($prev_id) {
            return $this->get_order(intval($prev_id));
        } else {
            return false;
        }
    }
}
