<?php

namespace MagentoTest\CustomerLinkedin\Plugin\Controller\Account;


use Closure;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Controller\Account\EditPost;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\UrlFactory;
use Magento\Framework\UrlInterface;
use Magento\Framework\Validator\Url;

class RestrictCustomerEditFormUrl
{

    /**
     * @var CustomerFactory
     */
    protected CustomerFactory $customerFactory;
    /**
     * @var RedirectFactory
     */
    private RedirectFactory $resultRedirectFactory;
    /**
     * @var ManagerInterface
     */
    private ManagerInterface $messageManager;
    /**
     * @var Url
     */
    private Url $urlValidator;
    /**
     * @var UrlInterface
     */
    private UrlInterface $urlModel;

    /**
     * RestrictCustomerEmail constructor.
     * @param UrlFactory $urlFactory
     * @param RedirectFactory $redirectFactory
     * @param ManagerInterface $messageManager
     * @param Url $urlValidator
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        UrlFactory $urlFactory,
        RedirectFactory $redirectFactory,
        ManagerInterface $messageManager,
        Url $urlValidator,
        CustomerRepositoryInterface $customerRepository
    )
    {
        $this->customerRepository = $customerRepository;
        $this->resultRedirectFactory = $redirectFactory;
        $this->messageManager = $messageManager;
        $this->urlValidator = $urlValidator;
        $this->urlModel = $urlFactory->create();

    }

    /**
     * @param EditPost $subject
     * @param Closure $proceed
     * @return mixed
     * @throws LocalizedException
     */
    public function aroundExecute(
        EditPost $subject,
        Closure $proceed
    )
    {
        /** @var RequestInterface $request */
        $linkedin_url = $subject->getRequest()->getParam('linkedin_url');
        if (!$this->urlValidator->isValid($linkedin_url)) {

            $this->messageManager->addErrorMessage(
                __('Please enter a correct URL value format ex:https://******')
            );
            $defaultUrl = $this->urlModel->getUrl('*/*/edit');
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setUrl($defaultUrl);

        }


        return $proceed();
    }
}
