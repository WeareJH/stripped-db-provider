<?php

declare(strict_types=1);

namespace Jh\StrippedDbProvider\Model\Db;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\ResourceConnection;
use Jh\StrippedDbProvider\Model\Config;

class DbTables
{
    private $defaultStructureOnlyTables = [
        'admin_passwords',
        'admin_system_messages',
        'admin_user',
        'admin_user_session',
        'adminnotification_inbox',
        'adyen_invoice',
        'adyen_notification',
        'adyen_order_payment',
        'algoliasearch_queue',
        'algoliasearch_queue_archive',
        'algoliasearch_queue_log',
        'catalogsearch_fulltext_cl',
        'catalogsearch_fulltext_scope1',
        'catalogsearch_recommendations',
        'company',
        'company_advanced_customer_entity',
        'company_credit',
        'company_credit_history',
        'company_order_entity',
        'company_payment',
        'company_permissions',
        'company_roles',
        'company_shipping',
        'company_structure',
        'company_team',
        'company_user_roles',
        'customer_address_entity',
        'customer_address_entity_datetime',
        'customer_address_entity_decimal',
        'customer_address_entity_int',
        'customer_address_entity_text',
        'customer_address_entity_varchar',
        'customer_entity',
        'customer_entity_datetime',
        'customer_entity_decimal',
        'customer_entity_int',
        'customer_entity_text',
        'customer_entity_varchar',
        'customer_grid_flat',
        'customer_log',
        'customer_visitor',
        'email_abandoned_cart',
        'email_automation',
        'email_campaign',
        'email_contact',
        'import_lock',
        'jh_logging',
        'jh_logging_issue',
        'jh_import_history',
        'jh_import_history_log',
        'jh_import_history_item_log',
        'jh_import_archive_csv',
        'magento_bulk',
        'magento_operation',
        'magento_customerbalance',
        'magento_customerbalance_history',
        'magento_customersegment_customer',
        'magento_giftcardaccount',
        'magento_invitation',
        'magento_invitation_status_history',
        'magento_invitation_track',
        'magento_logging_event',
        'magento_logging_event_changes',
        'magento_reward',
        'magento_reward_history',
        'magento_rma',
        'magento_rma_grid',
        'magento_rma_item_entity',
        'magento_rma_shipping_label',
        'magento_rma_status_history',
        'magento_sales_creditmemo_grid_archive',
        'magento_sales_invoice_grid_archive',
        'magento_sales_order_grid_archive',
        'magento_sales_shipment_grid_archive',
        'newsletter_problem',
        'newsletter_queue',
        'newsletter_queue_link',
        'newsletter_queue_store_link',
        'newsletter_subscriber',
        'nosto_index_product_cl',
        'nosto_index_product_queue_cl',
        'nosto_index_product_queue_processor_cl',
        'nosto_tagging_customer',
        'nosto_tagging_product_update_queue',
        'paypal_billing_agreement',
        'paypal_billing_agreement_order',
        'paypal_payment_transaction',
        'paypal_settlement_report',
        'paypal_settlement_report_row',
        'persistent_session',
        'product_alert_price',
        'product_alert_stock',
        'quote',
        'quote_address',
        'quote_address_item',
        'quote_id_mask',
        'quote_item',
        'quote_item_option',
        'quote_payment',
        'quote_preview',
        'quote_shipping_rate',
        'report_compared_product_index',
        'report_event',
        'report_viewed_product_aggregated_daily',
        'report_viewed_product_aggregated_monthly',
        'report_viewed_product_aggregated_yearly',
        'report_viewed_product_index',
        'sales_bestsellers_aggregated_daily',
        'sales_bestsellers_aggregated_monthly',
        'sales_bestsellers_aggregated_yearly',
        'sales_creditmemo',
        'sales_creditmemo_comment',
        'sales_creditmemo_grid',
        'sales_creditmemo_item',
        'sales_invoice',
        'sales_invoice_comment',
        'sales_invoice_grid',
        'sales_invoice_item',
        'sales_invoiced_aggregated',
        'sales_invoiced_aggregated_order',
        'sales_order',
        'sales_order_address',
        'sales_order_aggregated_created',
        'sales_order_aggregated_updated',
        'sales_order_grid',
        'sales_order_item',
        'sales_order_payment',
        'sales_order_status_history',
        'sales_order_tax',
        'sales_order_tax_item',
        'sales_payment_transaction',
        'sales_refunded_aggregated',
        'sales_refunded_aggregated_order',
        'sales_shipment',
        'sales_shipment_comment',
        'sales_shipment_grid',
        'sales_shipment_item',
        'sales_shipment_track',
        'sales_shipping_aggregated',
        'sales_shipping_aggregated_order',
        'search_query',
        'search_synonyms',
        'session',
        'support_report',
        'support_backup',
        'support_backup_item',
        'vault_payment_token',
        'vault_payment_token_order_payment_link',
        'wishlist',
        'wishlist_item',
        'wishlist_item_option',
        'yotpo_order_status_history',
        'yotpo_rich_snippets',
        'yotpo_sync'
    ];

    public function __construct(
        private DeploymentConfig $deploymentConfig,
        private ResourceConnection $resourceConnection
    ) {
    }

    public function getStructureOnlyTables(): array
    {
        $configPath = 'system/default/' . Config::XML_PATH_PROJECT_IGNORE_TABLES;
        $projectIgnoredTables = $this->deploymentConfig->get($configPath, []);
        $tables = array_merge($this->defaultStructureOnlyTables, $projectIgnoredTables);

        $connection = $this->resourceConnection->getConnection();
        foreach ($tables as $idx => $table) {
            if ($connection->isTableExists($table) === false) {
                unset($tables[$idx]);
            }
        }
        return $tables;
    }
}
