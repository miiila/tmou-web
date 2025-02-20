<?php declare(strict_types=1);
namespace InstruktoriBrno\TMOU\Forms;

use Nette\Application\UI\Form;
use Nette\SmartObject;

class TeamResetPasswordFormFactory
{
    use SmartObject;

    /** @var FormFactory */
    private $factory;

    public function __construct(FormFactory $factory)
    {
        $this->factory = $factory;
    }
    public function create(callable $onSuccess): Form
    {
        $form = $this->factory->create();

        $form->addText('email', 'E-mail')
            ->setRequired('Vyplňte, prosím, e-mail vašeho týmu. Pokud jste zapomněli i ten, kontaktujte prosím organizátory.')
            ->addRule(Form::MAX_LENGTH, 'E-mail může být maximálně 255 znaků dlouhý.', 255);
        $form->addText('token', 'Kód')
            ->setRequired('Vyplňte, prosím, kód k ověření žádosti o nové heslo, naleznete jej v e-mailu.');
        $form->addPassword('password', 'Nové heslo')
            ->setRequired('Vyplňte, prosím, nové heslo vašeho týmu.')
            ->addRule(Form::MIN_LENGTH, 'Délka hesla musí být alespoň 8 znaků.', 8);
        $form->addPassword('password2', 'Nové heslo znovu')
            ->setRequired('Vyplňte, prosím, nové heslo pro kontrolu shody.')
            ->addRule(Form::EQUAL, 'Hesla se musí shodovat.', $form['password']);

        $form->addPrimarySubmit('send', 'Požádat o nové heslo');
        $form->onSuccess[] = function (Form $form, $values) use ($onSuccess) {
            $onSuccess($form, $values);
        };
        return $form;
    }
}
