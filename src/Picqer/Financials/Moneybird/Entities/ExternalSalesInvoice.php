<?php

namespace Picqer\Financials\Moneybird\Entities;

use Picqer\Financials\Moneybird\Model;
use Picqer\Financials\Moneybird\Actions\FindAll;
use Picqer\Financials\Moneybird\Actions\FindOne;
use Picqer\Financials\Moneybird\Actions\Storable;
use Picqer\Financials\Moneybird\Actions\Removable;
use Picqer\Financials\Moneybird\Exceptions\ApiException;

/**
 * Class ExternalSalesInvoice.
 */
class ExternalSalesInvoice extends Model
{
    use FindAll, FindOne, Storable, Removable;

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'contact_id',
        'reference',
        'date',
        'due_date',
        'entry_number',
        'state',
        'currency',
        'prices_are_incl_tax',
        'source',
        'source_url',
        'origin',
        'paid_at',
        'total_paid',
        'total_unpaid',
        'total_price_excl_tax',
        'total_price_excl_tax_base',
        'total_price_incl_tax',
        'total_price_incl_tax_base',
        'created_at',
        'updated_at',
        'details',
        'payments',
        'notes',
        'attachments',
        'version',
    ];

    /**
     * @var string
     */
    protected $endpoint = 'external_sales_invoices';

    /**
     * @var string
     */
    protected $namespace = 'external_sales_invoice';

    /**
     * @var array
     */
    protected $singleNestedEntities = [
        'contact' => Contact::class,
    ];

    /**
     * @var array
     */
    protected $multipleNestedEntities = [
        'details' => [
            'entity' => ExternalSalesInvoiceDetail::class,
            'type' => self::NESTING_TYPE_ARRAY_OF_OBJECTS,
        ],
        'payments' => [
            'entity' => ExternalSalesInvoicePayment::class,
            'type' => self::NESTING_TYPE_ARRAY_OF_OBJECTS,
        ],
    ];

    /**
     * Create a payment for the current external sales invoice.
     *
     * @param ExternalSalesInvoicePayment $ExternalSalesInvoicePayment (payment_date and price are required)
     * @return $this
     * @throws ApiException
     */
    public function createPayment(ExternalSalesInvoicePayment $externalSalesInvoicePayment)
    {
        if (! isset($externalSalesInvoicePayment->payment_date)) {
            throw new ApiException('Required [payment_date] is missing');
        }

        if (! isset($externalSalesInvoicePayment->price)) {
            throw new ApiException('Required [price] is missing');
        }

        $this->connection()->post($this->endpoint . '/' . $this->id . '/payments',
            $externalSalesInvoicePayment->jsonWithNamespace()
        );

        return $this;
    }
}
