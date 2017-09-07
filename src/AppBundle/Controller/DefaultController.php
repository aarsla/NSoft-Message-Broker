<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Account;
use AppBundle\Entity\Message;
use AppBundle\Form\MessageType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="app_home")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Message $message
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, Message $message = null)
    {
        $account = $this->getAccount();

        if (!$message) {
            $message = new Message();
        }

        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->container->get('service_a')->process($message);
            $this->get('session')->getFlashBag()->add(
                'success',
                money_format('%i', $message->getAmount()).' '.$message->getCurrency().' sent!'
            );

            return $this->redirectToRoute('app_home');
        }

        return $this->render('AppBundle:Default:index.html.twig', [
            'account' => $account,
            'message' => $message,
            'form' => $form->createView()
        ]);
    }

    /**
     * @return Account
     */
    private function getAccount()
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Account::class);
        $account = $repository->getBalance();

        // Initialize 0.00 balance
        if (!$account) {
            $account = new Account();
            $account->setBalance(0);
            $account->setCurrency('EUR');
            $em->persist($account);
            $em->flush();
        }

        return $account;
    }

    /**
     * @Route("/account/reset", name="app_account_reset")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function resetAccount(Request $request)
    {
        // Reset balance
        $account = $this->getAccount();
        $account->setBalance(0);
        $account->setCreatedAt(new \DateTime());
        $account->setUpdatedAt(new \DateTime());

        $em = $this->getDoctrine()->getManager();
        $em->persist($account);
        $em->flush();

        // Truncate message database table
        $connection = $em->getConnection();
        $connection->executeQuery('SET FOREIGN_KEY_CHECKS = 0;');
        $platform = $connection->getDatabasePlatform();
        $truncateSql = $platform->getTruncateTableSQL('message');
        $connection->executeUpdate($truncateSql);
        $connection->executeQuery('SET FOREIGN_KEY_CHECKS = 1;');

        return $this->redirectToRoute('app_home');
    }
}
