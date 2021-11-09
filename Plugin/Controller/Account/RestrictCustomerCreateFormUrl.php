<?php

namespace MagentoTest\CustomerLinkedin\Plugin\Controller\Account;

use Closure;
use Magento\Customer\Controller\Account\CreatePost;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Customer\Model\ResourceModel\Customer\Collection;
use Magento\Framework\UrlInterface;
use Magento\Framework\Validator\Url;

/**
 * @codeCoverageIgnore
 */
class RestrictCustomerCreateFormUrl
{

    /** @var UrlInterface */
    protected UrlInterface $urlModel;

    /**
     * @var RedirectFactory
     */
    protected RedirectFactory $resultRedirectFactory;

    /**
     * @var ManagerInterface
     */
    protected ManagerInterface $messageManager;
    /**
     * @var Collection
     */
    protected Collection $_customerCollection;
    /**
     * @var Url
     */
    private Url $urlValidator;

    /**
     * RestrictCustomerEmail constructor.
     * @param UrlFactory $urlFactory
     * @param RedirectFactory $redirectFactory
     * @param ManagerInterface $messageManager
     * @param Collection $customerCollection
     */
    public function __construct(
        UrlFactory $urlFactory,
        RedirectFactory $redirectFactory,
        ManagerInterface $messageManager,
        Url $urlValidator,
        Collection $customerCollection

    )
    {
        $this->urlModel = $urlFactory->create();
        $this->_customerCollection = $customerCollection;
        $this->resultRedirectFactory = $redirectFactory;
        $this->messageManager = $messageManager;
        $this->urlValidator = $urlValidator;
    }

    /**
     * @param CreatePost $subject
     * @param Closure $proceed
     * @return mixed
     * @throws LocalizedException
     */
    public function aroundExecute(
        CreatePost $subject,
        Closure $proceed
    )
    {
        /** @var RequestInterface $request */
        $linkedin_profile = $subject->getRequest()->getParam('linkedin_profile');
        if (!$this->urlValidator->isValid($linkedin_profile)) {

            $this->messageManager->addErrorMessage(
                __('Please enter a correct URL value format ex:https://******')
            );
            $defaultUrl = $this->urlModel->getUrl('*/*/create');
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setUrl($defaultUrl);

        }
        return $proceed();
    }
}
