<?php declare(strict_types=1);
namespace InstruktoriBrno\TMOU\Forms;

use Nette\Application\UI\Form;
use Nette\SmartObject;

class TeamForgottenPasswordFormFactory
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

        $form->addPrimarySubmit('send', 'Požádat o nové heslo');
        $form->onSuccess[] = function (Form $form, $values) use ($onSuccess) {
            $onSuccess($form, $values);
        };
        return $form;
    }
}
