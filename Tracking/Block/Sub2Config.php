<?php

namespace Sub2\Tracking\Block;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\Order;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Store\Model\StoreManagerInterface;

class Sub2Config extends Template
{
    public const XML_PATH_ENABLED = 'tracking/general/enabled';
    public const XML_PATH_ENABLED_ADDTOCART = 'tracking/general/enabled_addtocart_tracking';
    public const XML_PATH_ENABLED_EMAIL_CAPTURED = 'tracking/general/enabled_email_capture_tracking';
    public const XML_PATH_LICENSE_KEY = 'tracking/general/license_key';

    /**
     * @var ScopeInterface
     */
    protected ScopeInterface $scopeConfigInterface;

    /**
     * @var CustomerSession
     */
    protected CustomerSession $customerSession;

    /**
     * @var CheckoutSession
     */
    protected CheckoutSession $checkoutSession;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     *  Sub2Config Constructor
     *
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfigInterface
     * @param CustomerSession $customerSession
     * @param CheckoutSession $checkoutSession
     * @param StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Context               $context,
        ScopeConfigInterface  $scopeConfigInterface,
        CustomerSession       $customerSession,
        CheckoutSession       $checkoutSession,
        StoreManagerInterface $storeManager,
        array                 $data = []
    ) {
        $this->scopeConfig = $scopeConfigInterface;
        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        $this->storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    /**
     *  Is module enabled
     *
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getStoreId()
        );
    }

    /**
     * Check if the addtocart is enabled for quote action track
     *
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isAddtocartEnabled(): bool
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ENABLED_ADDTOCART,
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getStoreId()
        );
    }

    /**
     * Check if the Emailcapture is enabled
     *
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isEmailCaptureEnabled(): bool
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ENABLED_EMAIL_CAPTURED,
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getStoreId()
        );
    }

    /**
     * Get License Key for track
     *
     * @return string|null
     * @throws NoSuchEntityException
     */
    public function getLicenseKey(): ?string
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_LICENSE_KEY,
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getStoreId()
        );
    }

    /**
     * Retrieve customer email
     *
     * @return string
     */
    public function getCustomerEmail(): string
    {
        if ($this->customerSession->isLoggedIn()) {
            return $this->customerSession->getCustomer()->getEmail(); // get Email
        }
        return '';
    }

    /**
     * Retrieve currency symbol
     *
     * @throws NoSuchEntityException
     */
    public function getStoreCurrencySymbol(): ?string
    {
        return $this->storeManager->getStore()->getCurrentCurrencyCode();
    }

    /**
     * Return order details
     *
     * @return array
     */
    public function getOrderDetails(): array
    {
        $result = [];

        $order = $this->checkoutSession->getLastRealOrder();
        if ($order->getIncrementId()) {
            $result['order_id'] = $order->getIncrementId();
            $result['coupon_code'] = $order->getCouponCode();
            $result['total'] = $order->getSubtotal() + $order->getDiscountAmount();
            $result['currency_code'] = $order->getOrderCurrencyCode();
            $result['post_code'] = $order->getShippingAddress()->getPostCode();
            $result['items'] = $this->getOrderItems($order);
        }
        return $result;
    }

    /**
     * Return item order details
     *
     * @param Order $order
     * @return array
     */
    public function getOrderItems(Order $order): array
    {
        $result = [];
        $items = $order->getAllVisibleItems();
        foreach ($items as $item) {
            $productInfo['product_id'] = $item->getProductId();
            $productInfo['sku'] = $item->getSku();
            $productInfo['name'] = $item->getName();
            $productInfo['qty'] = $item->getQtyOrdered();
            $productInfo['price'] = $item->getPrice();
            $result[] = $productInfo;
        }
        return $result;
    }
}
